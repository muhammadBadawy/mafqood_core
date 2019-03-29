# -*- coding: utf-8 -*-
"""
Created on Sat Dec 29 07:37:24 2018

@author: badawy
"""

from PIL import Image
import numpy as np                                 # (pip install numpy)
from skimage import measure                        # (pip install scikit-image)
from shapely.geometry import Polygon, MultiPolygon # (pip install Shapely)
import json
import PIL
import datetime



INFO = {
    "description": "Segmented clothes dataset",
    "url": "https://github.com/bearpaw/clothing-co-parsing",
    "version": "0.1.0",
    "year": 2019,
    "contributor": "bearpaw",
    "date_created": datetime.datetime.utcnow().isoformat(' ')
}

LICENSES = [
    {
        "id": 1,
        "name": "Apache License 2.0",
        "url": "https://raw.githubusercontent.com/bearpaw/clothing-co-parsing/master/LICENSE"
    }
]

CATEGORIES = [
    {
         "id": 0,
         "name": "ground",
         "supercategory": "ground"
         },
         {
         "id": 1,
         "name": "accessories",
         "supercategory": "accessories"
         },
         {
         "id": 2,
         "name": "bag",
         "supercategory": "hold"
         },
         {
         "id": 3,
         "name": "belt",
         "supercategory": "accessories"
         },
         {
         "id": 4,
         "name": "blazer",
         "supercategory": "top"
         },
         {
         "id": 5,
         "name": "blouse",
         "supercategory": "top"
         },
         {
         "id": 6,
         "name": "bodysuit",
         "supercategory": "top"
         },
         {
         "id": 7,
         "name": "boots",
         "supercategory": "foot"
         },
         {
         "id": 8,
         "name": "bra",
         "supercategory": "top"
         },
         {
         "id": 9,
         "name": "bracelet",
         "supercategory": "accessories"
         },
         {
         "id": 10,
         "name": "cape",
         "supercategory": "top"
         },
         {
         "id": 11,
         "name": "cardigan",
         "supercategory": "top"
         },
         {
         "id": 12,
         "name": "clogs",
         "supercategory": "foot"
         },
         {
         "id": 13,
         "name": "coat",
         "supercategory": "top"
         },
         {
         "id": 14,
         "name": "dress",
         "supercategory": "top"
         },
         {
         "id": 15,
         "name": "earrings",
         "supercategory": "accessories"
         },
         {
         "id": 16,
         "name": "flats",
         "supercategory": "foot"
         },
         {
         "id": 17,
         "name": "glasses",
         "supercategory": "face"
         },
         {
         "id": 18,
         "name": "gloves",
         "supercategory": "hand"
         },
         {
         "id": 19,
         "name": "hair",
         "supercategory": "hair"
         },
         {
         "id": 20,
         "name": "hat",
         "supercategory": "head"
         },
         {
         "id": 21,
         "name": "heels",
         "supercategory": "foot"
         },
         {
         "id": 22,
         "name": "hoodie",
         "supercategory": "head"
         },
         {
         "id": 23,
         "name": "intimate",
         "supercategory": "intimate"
         },
         {
         "id": 24,
         "name": "jacket",
         "supercategory": "top"
         },
         {
         "id": 25,
         "name": "jeans",
         "supercategory": "leg"
         },
         {
         "id": 26,
         "name": "jumper",
         "supercategory": "top"
         },
         {
         "id": 27,
         "name": "leggings",
         "supercategory": "leg"
         },
         {
         "id": 28,
         "name": "loafers",
         "supercategory": "foot"
         },
         {
         "id": 29,
         "name": "necklace",
         "supercategory": "accessories"
         },
         {
         "id": 30,
         "name": "panties",
         "supercategory": "intimate"
         },
         {
         "id": 31,
         "name": "pants",
         "supercategory": "leg"
         },
         {
         "id": 32,
         "name": "pumps",
         "supercategory": "top"
         },
         {
         "id": 33,
         "name": "purse",
         "supercategory": "hold"
         },
         {
         "id": 34,
         "name": "ring",
         "supercategory": "accessories"
         },
         {
         "id": 35,
         "name": "romper",
         "supercategory": "top"
         },
         {
         "id": 36,
         "name": "sandals",
         "supercategory": "foot"
         },
         {
         "id": 37,
         "name": "scarf",
         "supercategory": "scarf"
         },
         {
         "id": 38,
         "name": "shirt",
         "supercategory": "top"
         },
         {
         "id": 39,
         "name": "shoes",
         "supercategory": "foot"
         },
         {
         "id": 40,
         "name": "shorts",
         "supercategory": "leg"
         },
         {
         "id": 41,
         "name": "skin",
         "supercategory": "skin"
         },
         {
         "id": 42,
         "name": "skirt",
         "supercategory": "leg"
         },
         {
         "id": 43,
         "name": "sneakers",
         "supercategory": "foot"
         },
         {
         "id": 44,
         "name": "socks",
         "supercategory": "foot"
         },
         {
         "id": 45,
         "name": "stockings",
         "supercategory": "foot"
         },
         {
         "id": 46,
         "name": "suit",
         "supercategory": "suit"
         },
         {
         "id": 47,
         "name": "sunglasses",
         "supercategory": "face"
         },
         {
         "id": 48,
         "name": "sweater",
         "supercategory": "top"
         },
         {
         "id": 49,
         "name": "sweatshirt",
         "supercategory": "top"
         },
         {
         "id": 50,
         "name": "swimwear",
         "supercategory": "swimwear"
         },
         {
         "id": 51,
         "name": "t-shirt",
         "supercategory": "top"
         },
         {
         "id": 52,
         "name": "tie",
         "supercategory": "suit"
         },
         {
         "id": 53,
         "name": "tights",
         "supercategory": "leg"
         },
         {
         "id": 54,
         "name": "top",
         "supercategory": "top"
         },
         {
         "id": 55,
         "name": "vest",
         "supercategory": "top"
         },
         {
         "id": 56,
         "name": "wallet",
         "supercategory": "hold"
         },
         {
         "id": 57,
         "name": "watch",
         "supercategory": "accessories"
         },
         {
         "id": 58,
         "name": "wedges",
         "supercategory": "foot"
         },
]





coco_output = {
        "info": INFO,
        "licenses": LICENSES,
        "categories": CATEGORIES,
        "images": [],
        "annotations": []
    }




def create_sub_masks(mask_image):
    width, height = mask_image.size

    # Initialize a dictionary of sub-masks indexed by RGB colors
    sub_masks = {}
    for x in range(width):
        for y in range(height):
            # Get the RGB values of the pixel
            pixel = mask_image.getpixel((x,y))[:3]

            # If the pixel is not black...
            if pixel != (0, 0, 0):
                # Check to see if we've created a sub-mask...
                pixel_str = str(pixel)
                sub_mask = sub_masks.get(pixel_str)
                if sub_mask is None:
                   # Create a sub-mask (one bit per pixel) and add to the dictionary
                    # Note: we add 1 pixel of padding in each direction
                    # because the contours module doesn't handle cases
                    # where pixels bleed to the edge of the image
                    sub_masks[pixel_str] = Image.new('1', (width+2, height+2))

                # Set the pixel value to 1 (default is 0), accounting for padding
                sub_masks[pixel_str].putpixel((x+1, y+1), 1)

    return sub_masks


def create_sub_mask_annotation(sub_mask, image_id, category_id, annotation_id, is_crowd):
    # Find contours (boundary lines) around each sub-mask
    # Note: there could be multiple contours if the object
    # is partially occluded. (E.g. an elephant behind a tree)
    contours = measure.find_contours(sub_mask, 0.5, positive_orientation='low')

    segmentations = []
    polygons = []
    for contour in contours:
        # Flip from (row, col) representation to (x, y)
        # and subtract the padding pixel
        for i in range(len(contour)):
            row, col = contour[i]
            contour[i] = (col - 1, row - 1)

        # Make a polygon and simplify it
        poly = Polygon(contour)
        poly = poly.simplify(1.0, preserve_topology=False)
        polygons.append(poly)
        segmentation = np.array(poly.exterior.coords).ravel().tolist()
        segmentations.append(segmentation)

    # Combine the polygons to calculate the bounding box and area
    multi_poly = MultiPolygon(polygons)
    x, y, max_x, max_y = multi_poly.bounds
    width = max_x - x
    height = max_y - y
    bbox = (x, y, width, height)
    area = multi_poly.area

    annotation = {
        'segmentation': segmentations,
        'iscrowd': is_crowd,
        'image_id': image_id,
        'category_id': category_id,
        'id': annotation_id,
        'bbox': bbox,
        'area': area
    }

    return annotation

mask_images = []

name=""
for s in range(1,1001):
    if(s<10):
        image_file = PIL.Image.open('flipped/000'+str(s)+'.png')
        name="000"+str(s)
    elif(s<100 and s>9):
        image_file = PIL.Image.open('flipped/00' + str(s) + '.png')
        name = "00" + str(s)
    elif(s<1000 and s>99):
        image_file = PIL.Image.open('flipped/0' + str(s) + '.png')
        name = "0" + str(s)
    elif (s == 1000):
        image_file = PIL.Image.open('flipped/' + str(s) + '.png')
        name = str(s)
    
    
    image_info = {
            "id": s,
            "license": 1,
            "file_name": 'flipped/' + name + '.png',
            "height": image_file.height,
            "width": image_file.width
        }
    
    coco_output["images"].append(image_info)
    
    mask_images.append(image_file)

#plant_book_mask_image = Image.open('imgs/0001.png')
#bottle_book_mask_image = Image.open('/path/to/images/bottle_book_mask.png')

#mask_images = [plant_book_mask_image]

# Define which colors match which categories in the images
houseplant_id, book_id, bottle_id, lamp_id = [1, 2, 3, 4]
category_ids = {
    '(0, 0, 0)' : 0,
    '(255, 191, 191)' : 1,
    '(0, 0, 128)' : 2,
    '(0, 0, 255)' : 3,
    '(0, 64, 64)' : 4,
    '(0, 64, 191)' : 5,
    '(0, 128, 0)' : 6,
    '(0, 128, 128)' : 7,
    '(0, 128, 255)' : 8,
    '(0, 191, 64)' : 9,
    '(0, 191, 191)' : 10,
    '(0, 255, 0)' : 11,
    '(0, 255, 128)' : 12,
    '(0, 255, 255)' : 13,
    '(64, 0, 0)' : 14,
    '(64, 0, 128)' : 15,
    '(64, 0, 255)' : 16,
    '(64, 64, 64)' : 17,
    '(64, 64, 191)' : 18,
    '(64, 128, 0)' : 19,
    '(64, 128, 128)' : 20,
    '(64, 128, 255)' : 21,
    '(64, 191, 64)' : 22,
    '(64, 191, 191)' : 23,
    '(64, 255, 0)' : 24,
    '(64, 255, 128)' : 25,
    '(64, 255, 255)' : 26,
    '(128, 0, 0)' : 27,
    '(128, 0, 128)' : 28,
    '(128, 0, 255)' : 29,
    '(128, 64, 64)' : 30,
    '(128, 64, 191)' : 31,
    '(128, 128, 0)' : 32,
    '(128, 128, 128)' : 33,
    '(128, 128, 255)' : 34,
    '(128, 191, 64)' : 35,
    '(128, 191, 191)' : 36,
    '(128, 255, 0)' : 37,
    '(128, 255, 128)' : 38,
    '(128, 255, 255)' : 39,
    '(191, 0, 0)' : 40,
    '(191, 0, 128)' : 41,
    '(191, 0, 255)' : 42,
    '(191, 64, 64)' : 43,
    '(191, 64, 191)' : 44,
    '(191, 128, 0)' : 45,
    '(191, 128, 128)' : 46,
    '(191, 128, 255)' : 47,
    '(191, 191, 64)' : 48,
    '(191, 191, 191)' : 49,
    '(191, 255, 0)' : 50,
    '(191, 255, 128)' : 51,
    '(191, 255, 255)' : 52,
    '(255, 0, 0)' : 53,
    '(255, 0, 128)' : 54,
    '(255, 0, 255)' : 55,
    '(255, 64, 64)' : 56,
    '(255, 64, 191)' : 57,
    '(255, 128, 0)' : 58,
    '(255, 128, 128)' : 59
}

is_crowd = 0

# These ids will be automatically increased as we go
annotation_id = 1
image_id = 1

# Create the annotations
annotations = []
for mask_image in mask_images:
    sub_masks = create_sub_masks(mask_image)
    for color, sub_mask in sub_masks.items():
        category_id = category_ids[color]
        annotation = create_sub_mask_annotation(sub_mask, image_id, category_id, annotation_id, is_crowd)
        annotations.append(annotation)
        annotation_id += 1
    image_id += 1

#print(json.dumps(annotations))
coco_output["annotations"] = annotations
 
json = json.dumps(coco_output, indent=1, separators=(',', ': '))
f = open("segmented_clothes_dataset_3_2_10_56.json","w")
f.write(json)
f.close()