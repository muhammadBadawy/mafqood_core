# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import numpy as np
import tensorflow as tf
import cv2
import time
from PIL import Image


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
  
    def cutting(image_obj, coords, saved_location):

        """
    
        @param image_path: The path to the image to edit
    
        @param coords: A tuple of x/y coordinates (x1, y1, x2, y2)
    
        @param saved_location: Path to save the cropped image
    
        """
    
        #image_obj = Image.open(image_path)
                
        #image_obj = image_obj.rotate(270)
        
        #image_obj = image_obj.resize((1024, 682))
        
        x = coords[0];
        y = coords[1];
        w = coords[2];
        h = coords[3];
        
        cropped_image = image_obj[x:w, y:h]
    
        #cropped_image = image_obj.crop(coords)
    
        #cropped_image.save(saved_location)
        cv2.imwrite( saved_location, cropped_image )
        print( "Saved" )
    
        #cropped_image.show()

if __name__ == "__main__":
    model_path = 'faster_rcnn_inception_v2_coco/frozen_inference_graph.pb'
    odapi = DetectorAPI(path_to_ckpt=model_path)
    threshold = 0.7
    #1>RGP
    img=cv2.imread('p.jpg', 1)
    
    #cap = cv2.VideoCapture('/test.avi')

   # while True:
       # r, img = cap.read()
    img = cv2.resize(img, ( 1024, 682))
    #cropped_image.save(saved_location)
    cv2.imwrite( "gray_Image.jpg", img )
    boxes, scores, classes, num = odapi.processFrame(img)
    
    # Visualization of the results of a detection.
    
    ##
    print (str(len(boxes)))
    for i in range(len(boxes)):
        # Class 1 represents human
        if classes[i] == 1 and scores[i] > threshold:
            print ("entered")
            box = boxes[i]
            x,y,width,heigth=box
            print(img.shape)
            DetectorAPI.cutting(img, (x,y,width,heigth), "single_"+str(i)+".jpg")
            #new=tf.image.crop_to_bounding_box(img, y ,x , y+heigth,1102)
            #print(new)
            #img = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
            #image=Image.open("p.jpg")
            #cropped=image.crop((box))
            #cropped.save("i.jpg");
            #x, y, wi..dth, height=boxes[i]
            #print(box)
            #print(x,y,width,heigth)
            #cv2.imwrite("single"+str(i)+".png", img[box] )
           # newImg=img[y:y+height , x:x+width, :]
            ##
            #x, y, w, h =box
            #print(box[0])
            #roi = img[y:y+height, x:x+width]
            #cv2.imwrite("single"+i+".png", roi)
            ##cv2.rectangle(img, (x,y), (x+w, y+h), (255,0,0), 2)
            #newImg=cv2.rectangle(img,(y,x), (x+w, y+h),(255,0,0),2)
            #p#Obj=cv2.rectangle(img,(box[1],box[0]),(box[3],box[2]),(255,0,0),2)
            #pobj=img[837:927+837,275:551+275]
            #cv2.imwrite("single"+str(i)+".png",pobj )
    
   # cv2.imshow("preview", img )
    #key = cv2.waitKey(1)
   
    # if key & 0xFF == ord('q'):
    #    return 0
      

