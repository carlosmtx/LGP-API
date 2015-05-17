<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 07/05/2015
 * Time: 17:24
 */
namespace AppBundle\Service\TrackingXMLManager;

use AppBundle\Document\Scene;
use AppBundle\Document\Trackable;

class TrackingXMLManager {

    public function parse($xml){
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<TrackingData>
    <Sensors>
        <Sensor Subtype=\"FAST\" Type=\"FeatureBasedSensorSource\">
            <SensorID>FeatureBasedSensorSource_0</SensorID>
            <Parameters>
                <MaxObjectsToDetectPerFrame>5</MaxObjectsToDetectPerFrame>
                <MaxObjectsToTrackInParallel>1</MaxObjectsToTrackInParallel>
            </Parameters>
            <SensorCOS>
                <SensorCosID>5150fef06944f70fd5ba32b50c0d21c6</SensorCosID>
                <Parameters>
                    <SimilarityThreshold>0.3</SimilarityThreshold>
                    <ReferenceImage HeightMM=\"512\" WidthMM=\"512\">5150fef06944f70fd5ba32b50c0d21c6.jpg</ReferenceImage>
                </Parameters>
            </SensorCOS>
            <SensorCOS>
                <SensorCosID>2edb63a80b2e200e78c3bc4f15abf7d6</SensorCosID>
                <Parameters>
                    <SimilarityThreshold>0.3</SimilarityThreshold>
                    <ReferenceImage HeightMM=\"548\" WidthMM=\"548\">2edb63a80b2e200e78c3bc4f15abf7d6.jpg</ReferenceImage>
                </Parameters>
            </SensorCOS>
        </Sensor>
    </Sensors>
    <Connections>
        <COS>
            <Name>road_1</Name>
            <Fuser Type=\"SmoothingFuser\">
                <Parameters>
                    <AlphaRotation>0.8</AlphaRotation>
                    <AlphaTranslation>1.0</AlphaTranslation>
                    <GammaRotation>0.8</GammaRotation>
                    <GammaTranslation>1.0</GammaTranslation>
                    <KeepPoseForNumberOfFrames>0</KeepPoseForNumberOfFrames>
                </Parameters>
            </Fuser>
            <SensorSource>
                <SensorID>FeatureBasedSensorSource_0</SensorID>
                <SensorCosID>5150fef06944f70fd5ba32b50c0d21c6</SensorCosID>
                <HandEyeCalibration>
                    <TranslationOffset>
                        <x>0.0</x>
                        <y>0.0</y>
                        <z>0.0</z>
                    </TranslationOffset>
                    <RotationOffset>
                        <x>0.0</x>
                        <y>0.0</y>
                        <z>0.0</z>
                        <w>1.0</w>
                    </RotationOffset>
                </HandEyeCalibration>
                <COSOffset>
                    <TranslationOffset>
                        <x>-192.0</x>
                        <y>1.0</y>
                        <z>0.0</z>
                    </TranslationOffset>
                    <RotationOffset>
                        <x>0.0</x>
                        <y>0.0</y>
                        <z>0.0</z>
                        <w>1.0</w>
                    </RotationOffset>
                </COSOffset>
            </SensorSource>
        </COS>
        <COS>
            <Name>sun_2</Name>
            <Fuser Type=\"SmoothingFuser\">
                <Parameters>
                    <AlphaRotation>0.8</AlphaRotation>
                    <AlphaTranslation>1.0</AlphaTranslation>
                    <GammaRotation>0.8</GammaRotation>
                    <GammaTranslation>1.0</GammaTranslation>
                    <KeepPoseForNumberOfFrames>0</KeepPoseForNumberOfFrames>
                </Parameters>
            </Fuser>
            <SensorSource>
                <SensorID>FeatureBasedSensorSource_0</SensorID>
                <SensorCosID>2edb63a80b2e200e78c3bc4f15abf7d6</SensorCosID>
                <HandEyeCalibration>
                    <TranslationOffset>
                        <x>0.0</x>
                        <y>0.0</y>
                        <z>0.0</z>
                    </TranslationOffset>
                    <RotationOffset>
                        <x>0.0</x>
                        <y>0.0</y>
                        <z>0.0</z>
                        <w>1.0</w>
                    </RotationOffset>
                </HandEyeCalibration>
                <COSOffset>
                    <TranslationOffset>
                        <x>-171.0</x>
                        <y>0.5</y>
                        <z>0.0</z>
                    </TranslationOffset>
                    <RotationOffset>
                        <x>0.0</x>
                        <y>0.0</y>
                        <z>0.0</z>
                        <w>1.0</w>
                    </RotationOffset>
                </COSOffset>
            </SensorSource>
        </COS>
    </Connections>
</TrackingData>
";
        $this->xmlElem =  new \SimpleXMLElement($xml);
    }

    public function connect(Scene $scene , Trackable $trackable){

    }
}