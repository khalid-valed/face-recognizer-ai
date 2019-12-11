<?php
namespace Api\Model;

use CV\Face\FacemarkLBF;
use CV\Face\LBPHFaceRecognizer;
use CV\CascadeClassifier;
use CV\Scalar;
use CV\Size;
use CV\Point;
use const CV\{COLOR_BGR2GRAY};
use function CV\equalizeHist;
use function CV\imread;
use function CV\imdecode;
use function CV\imwrite;
use function CV\cvtColor;
use function CV\circle;
use function CV\rectangle;
use function CV\{rectangleByRect};

class Image
{
    public function cropFaceAndSave($image)
    {
        $faceClassifier = new CascadeClassifier();
        $faceClassifier->load(ROOT_DIR.'/api/Iconfigs/lbpcascades/lbpcascade_frontalface.xml');
        // me
        $src = imdecode($image);
        $gray = cvtColor($src, COLOR_BGR2GRAY);
        $faceClassifier->detectMultiScale($gray, $faces);
        //var_export($faces);
        equalizeHist($gray, $gray);
        $faceImages = $faceLabels = [];
        foreach ($faces as $k => $face) {
            $faceImages[] = $gray->getImageROI($face); // face coordinates to image
            $faceLabels[] = 1; // me
        imwrite(ROOT_DIR."/results/recognize_face_by_lbph_me$k.jpg", $gray->getImageROI($face));
        }
    }
    public function imageRead($path)
    {
        $src = imread(ROOT_DIR.$path) ;
        return $src;
    }
    public function recognizeFaceLbph($unkown, $user)
    {
        $faceClassifier = new CascadeClassifier();
        $faceClassifier->load(ROOT_DIR.'/api/Iconfigs/lbpcascades/lbpcascade_frontalface.xml');
        $faceRecognizer = LBPHFaceRecognizer::create();
        // unkown person
        $gray = cvtColor($unkown, COLOR_BGR2GRAY);
        $faceClassifier->detectMultiScale($gray, $faces);
        //var_export($faces);
        equalizeHist($gray, $gray);
        $faceImages = $faceLabels = [];
        foreach ($faces as $k => $face) {
            $faceImages[] = $gray->getImageROI($face); // face coordinates to image
            $faceLabels[] = 1; // me
        }
        $faceRecognizer->train($faceImages, $faceLabels);
        $gray = cvtColor($user, COLOR_BGR2GRAY);//test image
        $faceClassifier->detectMultiScale($gray, $faces);
        equalizeHist($gray, $gray);
        foreach ($faces as $face) {
            $faceImage = $gray->getImageROI($face);
            $faceLabel = $faceRecognizer->predict($faceImage, $faceConfidence);
        }
        return $faceConfidence;
    }
    public function classifyImageDnn()
    {
        $categories = explode("\n", file_get_contents(ROOT_DIR.'/api/Iconfigs/mobilenet/classes.txt'));
        $src = imread(ROOT_DIR."/images/cat.jpg"); // opencv loads image to matrix with BGR order
        $src = cvtColor($src, CV\COLOR_BGR2RGB); // convert BGR to RGB
        //var_export($src);
        $blob = DNN\blobFromImage($src, 0.017, new Size(224, 224), new Scalar(103.94, 116.78, 123.68)); // convert image to 4 dimensions matrix
        //var_export($blob);
        $net = DNN\readNetFromCaffe(ROOT_DIR.'/api/Iconfigs/mobilenet/mobilenet_deploy.prototxt', 'models/mobilenet/mobilenet.caffemodel');
        $net->setInput($blob, "");
        $r = $net->forward();
        //var_export($r);
        $maxConfidence = 0;
        $confidences = [];
        for ($i = 0; $i < 1000; $i++) {
            $confidences[$i] = intval($r->atIdx([0,$i,0,0], 1) * 100);
        }
        // top 5:
        arsort($confidences);
        $confidences = array_slice($confidences, 0, 5, true);

        foreach ($confidences as $label => $confidence) {
            echo "$confidence%: {$categories[$label]}\n";
        }
    }
    public function detectFaceByCascad($src)
    {
        $gray = cvtColor($src, COLOR_BGR2GRAY);
        $faceClassifier = new CascadeClassifier();
        $faceClassifier->load(ROOT_DIR.'/api/Iconfigs/lbpcascades/lbpcascade_frontalface.xml');
        $faceClassifier->detectMultiScale($gray, $faces);
        $result = false;
        if ($fases) {
            $result = true;
        }
        return $result;
        //  if ($faces) {
        //   $scalar = new Scalar(0, 0, 255); //blue
        //   foreach ($faces as $face) {
        //       rectangleByRect($src, $face, $scalar, 3);
        //   }
        //  }
        // eyes by haarcascade_eye
        /*$eyeClassifier = new CascadeClassifier();
        $eyeClassifier->load(ROOT_DIR.'/api/Iconfigs/haarcascades/haarcascade_eye.xml');
        $eyes = null;
        $eyeClassifier->detectMultiScale($gray, $eyes);
        var_export($eyes);
        if ($eyes) {
            $scalar = new Scalar(0, 0, 255); //red
            foreach ($eyes as $eye) {
                rectangle($src, $eye->x, $eye->y, $eye->x + $eye->width, $eye->y + $eye->height, $scalar, 2);
            }
        }*/
        // imwrite(ROOT_DIR."/results/_detect_face_by_cascade_classifier.jpg", $src);
    }
    public function detectFaceByDnnSsd()
    {
        $src = imread(ROOT_DIR."/images/faces.jpg");
        $size = $src->size(); // 2000x500
        $minSide = min($size->width, $size->height);
        $divider = $minSide / 300;
        resize($src, $resized, new Size($size->width / $divider, $size->height / $divider)); // 1200x300
        $blob = DNN\blobFromImage($resized, 1, new Size(), new Scalar(104, 177, 123), true, false);
        $net = DNN\readNetFromCaffe(ROOT_DIR.'/api/Iconfigs/ssd/res10_300x300_ssd_deploy.prototxt', 'models/ssd/res10_300x300_ssd_iter_140000.caffemodel');
        $net->setInput($blob, "");
        $r = $net->forward();
        var_export($r->shape);
        $scalar = new Scalar(0, 0, 255);
        for ($i = 0; $i < $r->shape[2]; $i++) {
            $confidence = $r->atIdx([0,0,$i,2]);
            if ($confidence > 0.9) {
                var_export($confidence);
                echo "\n";
                $startX = $r->atIdx([0,0,$i,3]) * $src->cols;
                $startY = $r->atIdx([0,0,$i,4]) * $src->rows;
                $endX = $r->atIdx([0,0,$i,5]) * $src->cols;
                $endY = $r->atIdx([0,0,$i,6]) * $src->rows;
                rectangle($src, $startX, $startY, $endX, $endY, $scalar, 3);
            }
        }
        $data = [];
        imwrite(ROOT_DIR."/results/_detect_face_by_dnn_ssd.jpg", $src);
    }
    public function detectFaceMarksLbf()
    {
        $src = imread(ROOT_DIR."/images/15.png");
        $gray = cvtColor($src, COLOR_BGR2GRAY);
        equalizeHist($gray, $gray);

        // face by lbpcascade_frontalface
        $faceClassifier = new CascadeClassifier();
        $faceClassifier->load(ROOT_DIR.'/api/Iconfigs/lbpcascades/lbpcascade_frontalface.xml');
        $faces = null;
        $faceClassifier->detectMultiScale($gray, $faces);
        //var_export($faces);

        $facemark = FacemarkLBF::create();
        $facemark->loadModel(ROOT_DIR.'/api/Iconfigs/opencv-facemark-lbf/lbfmodel.yaml');

        $facemark->fit($src, $faces, $landmarks);
        var_export($landmarks);
        //  if ($landmarks) {
      //      $scalar = new Scalar(0, 0, 255);
      //      foreach ($landmarks as $face) {
      //          foreach ($face as $k => $point) {//var_export($point);
      //              circle($src, $point, 2, $scalar, 2);
      //          }
      //      }
      //  }
      //  imwrite(ROOT_DIR."/results/_detect_facemarks_by_lbf.jpg", $src);
    }
    public function detectObjectsByDnn()
    {
        //$categories = explode("\n", file_get_contents(ROOT_DIR.'/api/Iconfigs/ssd_mobilenet_v1_coco/classes.txt'));
        $categories = explode("\n", file_get_contents(ROOT_DIR.'/api/Iconfigs/ssdlite_mobilenet_v2_coco/classes.txt'));

        $src = imread(ROOT_DIR."/images/objects.jpg"); // opencv loads image to matrix with BGR order
        //var_export($src);

        $blob = DNN\blobFromImage($src, 0.017, new Size(300, 300), new Scalar(127.5, 127.5, 127.5), true, false); // convert image to 4 dimensions matrix
        //var_export($blob);

        //$net = DNN\readNetFromTensorflow(ROOT_DIR.'/api/Iconfigs/ssd_mobilenet_v1_coco/frozen_inference_graph.pb', 'models/ssd_mobilenet_v1_coco/ssd_mobilenet_v1_coco.pbtxt');
        $net = DNN\readNetFromTensorflow(ROOT_DIR.'/api/Iconfigs/ssdlite_mobilenet_v2_coco/frozen_inference_graph.pb', 'models/ssdlite_mobilenet_v2_coco/ssdlite_mobilenet_v2_coco.pbtxt');
        $net->setInput($blob, "");

        $r = $net->forward();
        var_export($r);

        $rectangles = [];
        for ($i = 0; $i < $r->shape[2]; $i++) {
            $classId = $r->atIdx([0,0,$i,1]);
            $confidence = intval($r->atIdx([0,0,$i,2]) * 100);
            if ($classId && $confidence > 30) {
                $startX = $r->atIdx([0,0,$i,3]) * $src->cols;
                $startY = $r->atIdx([0,0,$i,4]) * $src->rows;
                $endX = $r->atIdx([0,0,$i,5]) * $src->cols;
                $endY = $r->atIdx([0,0,$i,6]) * $src->rows;

                $scalar = new Scalar(0, 0, 255);
                rectangle($src, $startX, $startY, $endX, $endY, $scalar, 2);

                $text = "{$categories[$classId]} $confidence%";
                rectangle($src, $startX, $startY + 10, $startX + 20 * strlen($text), $startY - 30, new Scalar(255, 255, 255), -2);
                putText($src, "{$categories[$classId]} $confidence%", new Point($startX, $startY - 2), 0, 1.5, new Scalar(), 2);
            }
        }

        imwrite(ROOT_DIR."/results/_detect_objects_by_dnn_mobilenet.png", $src);
    }
    public function updscaleImage()
    {
        $src = imread(ROOT_DIR."/images/icon_64x64.png"); // opencv loads image to matrix with BGR order
        $src = cvtColor($src, CV\COLOR_BGR2RGB); // convert BGR to RGB
        copyMakeBorder($src, $src, 7, 7, 7, 7, 1); // add borders 7px
        //var_export($src);
        $blob = DNN\blobFromImage($src, 1, $src->size(), new Scalar()); // convert image to 4 dimensions matrix
        //var_export($blob);
        $blob = $blob->divide(255); // convert color values from 0-255 to 0-1

        $net = DNN\readNetFromCaffe(ROOT_DIR.'/api/Iconfigs/waifu2x/scale2.0x_model.prototxt', 'models/waifu2x/scale2.0x_model.caffemodel');

        $net->setInput($blob, "");

        $r = $net->forward();

        $r = $r->divide(1/255); // convert color values from 0-255 to 0-1
        //var_export($r);

        $mat = new Mat($r->shape[2], $r->shape[3], CV\CV_32FC3);

        for ($i = 0; $i < $r->shape[2]; $i++) {
            for ($j = 0; $j < $r->shape[3]; $j++) {
                $mat->at($i, $j, 0, $r->atIdx([0,0,$i,$j])); //R
        $mat->at($i, $j, 1, $r->atIdx([0,1,$i,$j])); //G
        $mat->at($i, $j, 2, $r->atIdx([0,2,$i,$j])); //B
            }
        }
        imwrite(ROOT_DIR."/results/_upscale_image_x2_by_dnn_waifu2x.png", $mat);
    }
}
