# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import numpy as np
import tensorflow as tf
import cv2
import time
import face_recognition
import sys



class DetectorAPI:
    def __init__(self, path_to_ckpt):
        self.path_to_ckpt = path_to_ckpt

        self.detection_graph = tf.Graph()
        with self.detection_graph.as_default():
            od_graph_def = tf.GraphDef()
            with tf.gfile.GFile(self.path_to_ckpt, 'rb') as fid:
                serialized_graph = fid.read()
                od_graph_def.ParseFromString(serialized_graph)
                tf.import_graph_def(od_graph_def, name='')

        self.default_graph = self.detection_graph.as_default()
        self.sess = tf.Session(graph=self.detection_graph)

        # Definite input and output Tensors for detection_graph
        self.image_tensor = self.detection_graph.get_tensor_by_name('image_tensor:0')
        # Each box represents a part of the image where a particular object was detected.
        self.detection_boxes = self.detection_graph.get_tensor_by_name('detection_boxes:0')
        # Each score represent how level of confidence for each of the objects.
        # Score is shown on the result image, together with the class label.
        self.detection_scores = self.detection_graph.get_tensor_by_name('detection_scores:0')
        self.detection_classes = self.detection_graph.get_tensor_by_name('detection_classes:0')
        self.num_detections = self.detection_graph.get_tensor_by_name('num_detections:0')

    def processFrame(self, image):
        # Expand dimensions since the trained_model expects images to have shape: [1, None, None, 3]
        image_np_expanded = np.expand_dims(image, axis=0)
        # Actual detection.
        start_time = time.time()
        (boxes, scores, classes, num) = self.sess.run(
            [self.detection_boxes, self.detection_scores, self.detection_classes, self.num_detections],
            feed_dict={self.image_tensor: image_np_expanded})
        end_time = time.time()

        print("Elapsed Time:", end_time-start_time)

        im_height, im_width,_ = image.shape
        boxes_list = [None for i in range(boxes.shape[1])]
        for i in range(boxes.shape[1]):
            boxes_list[i] = (int(boxes[0,i,0] * im_height),
                        int(boxes[0,i,1]*im_width),
                        int(boxes[0,i,2] * im_height),
                        int(boxes[0,i,3]*im_width))

        return boxes_list, scores[0].tolist(), [int(x) for x in classes[0].tolist()], int(num[0])

    def close(self):
        self.sess.close()
        self.default_graph.close()
  
    def cutting(image_obj, coords, number):

        """
    
        @param image_path: The path to the image to edit
    
        @param coords: A tuple of x/y coordinates (x1, y1, x2, y2)
    
        @param saved_location: Path to save the cropped image
    
        """
        x = coords[0]
        y = coords[1]
        w = coords[2]
        h = coords[3]
        
        cropped_image = image_obj[x:w, y:h]
        return cropped_image
    
    
    def saveImage(image_obj, coords, name):
        
        x = coords[0]
        y = coords[1]
        w = coords[2]
        h = coords[3]
        
        cropped_image = image_obj[x:w, y:h]
    
        cv2.imwrite( '../OutputImages/'+str(name)+'.jpg', cropped_image )

    
        #cropped_image.show()

if __name__ == "__main__":
    #print(sys.argv[0])
    known_encodings =[]
    known_indexes =[]
    count=0
    model_path = '../faster_rcnn_inception_v2_coco/frozen_inference_graph.pb'
    odapi = DetectorAPI(path_to_ckpt=model_path)
    threshold = 0.7
    #1>RGP
#    img=cv2.imread(str(sys.argv[1]), 1)#../TestImages/multiObject.jpg
    img=cv2.imread('../TestImages/multiObject.jpg', 1)
#    face_detc_image = cv2.imread(str(sys.argv[2]))#../TestImages/faecTest/someOne.jpg
    face_detc_image = cv2.imread('../TestImages/singleObject.jpg')
    face_detc_image_rgp = cv2.cvtColor(face_detc_image, cv2.COLOR_BGR2RGB)
    test_face_encoding_array = face_recognition.face_encodings(face_detc_image_rgp)[0]
    #print (img)
    boxes, scores, classes, num = odapi.processFrame(img)
    # Visualization of the results of a detection.
    #print(len(img))
    for i in range(len(boxes)):
        # Class 1 represents human
        if classes[i] == 1 and scores[i] > threshold:
            count=count+1
            print ("entered>>"+str(count))
            box = boxes[i]
            x,y,width,heigth=box
            
            singleImage=DetectorAPI.cutting(img, (x,y,width,heigth), i)
            rgb = cv2.cvtColor(singleImage, cv2.COLOR_BGR2RGB)
            
            face_encoding_array = face_recognition.face_encodings(rgb)
            
            if len(face_encoding_array) >= 1:
                print('there is a face')
                face_encoding = face_encoding_array[0]
                #face_encoding_bytes = face_encoding.tobytes()
                known_encodings.append(face_encoding)
                known_indexes.append(i)
                #np.save('people/face_encoding'+str(i)+'.npy', face_encoding_bytes)
            else:
                print('there is no faces OR this face not matching')
    #print (known_encodings)     
    print(known_encodings)
    face_distances = face_recognition.face_distance(known_encodings, test_face_encoding_array)
    print(face_distances)
    
    known_tupples = []
    
    for j in range(len(face_distances)):
        known_tupples.append({'index':str(known_indexes[j]), 'distance':face_distances[j]})
        
    #print (face_distances) return all distances between test image and real face
    min_distance_array = [x['distance'] for x in known_tupples]
    min_distance = max(min_distance_array)
    
    min_index = 0
    for i in range(len(known_tupples)) :
        if known_tupples[i]['distance'] < min_distance :
            min_distance = known_tupples[i]['distance']
            min_index = known_tupples[i]['index']
            print('image number>>'+str(min_index))
    DetectorAPI.saveImage(img, boxes[int(min_index)], min_distance)
    print ( known_tupples ) 