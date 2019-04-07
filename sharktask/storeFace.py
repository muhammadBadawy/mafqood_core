## Code adapted from Tensorflow Object Detection Framework
## https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
## Tensorflow Object Detection Detector
#import cv2
#import face_recognition
#import json
#
##import tensorflow-human-detection 
#
#
#class FaceManipulation:
#    def storeFaces(multiFacesImage):
#        face_locations = face_recognition.face_locations(img)
#        face_encodings = face_recognition.face_encodings(img, face_locations)
#   #    print(json.dumps(tuple(zip(face_locations, face_encodings))))
#        output = {}
#        i=0
#        for face_location, face_encoding in zip(face_locations, face_encodings):
#            print(type(face_encoding))
#            face_encoding = face_encoding.tolist()
#            output[str(i)]={'print' :face_encoding , 'bbox':face_location}
#            i+=1
#        
#        return output
#    
#            
#
#    
#        #cropped_image.show()
#
#if __name__ == "__main__":
#    imagePath=str(sys.argv[1])
#    img=cv2.imread(imagePath, 1)
#    output = FaceManipulation.storeFaces(img)
##   output = output.tolist()
#    print(json.loads(json.dumps(output)))   
    
    
    
    
    
#############################################this code for testing ######################################
    
    # Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector
import cv2
import face_recognition
import json

#import tensorflow-human-detection 


class FaceManipulation:
    def storeFaces(multiFacesImage):
        face_locations = face_recognition.face_locations(img)
        face_encodings = face_recognition.face_encodings(img, face_locations)
   #    print(json.dumps(tuple(zip(face_locations, face_encodings))))
        output = {}
        i=0
        for face_location, face_encoding in zip(face_locations, face_encodings):
   #        print(type(face_encoding))
            face_encoding = face_encoding.tolist()
            output[str(i)]={'print' :face_encoding , 'bbox':face_location}
            i+=1
        
        return output
    
            

    
        #cropped_image.show()

if __name__ == "__main__":

    img=cv2.imread('../TestImages/large.jpg', 1)
    output = FaceManipulation.storeFaces(img)
#   output = output.tolist()
    print(json.loads(json.dumps(output)))   