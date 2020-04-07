<?php

namespace Impactaweb\Crud\Listing\Traits;

use Impactaweb\Crud\Listing\Listing;

trait FieldTypes {

    public function linkField(string $linkName, string $urlWithParameters, ?string $linkClass = null)
    {
        $callback = function($data) use ($linkName, $urlWithParameters, $linkClass) {
            $linkClass = $linkClass ?? '';
            $url = Listing::fillUrlParameters($urlWithParameters, $data);
            return '<a href="' . $url . '" class="' . $linkClass . '">' . $linkName . '</a>';
        };

        return $this->customField("", $callback);
    }

    public function buttonField(string $buttonName, string $urlWithParameters, ?string $buttonClass = null)
    {
        $buttonClass = ($buttonClass && !empty($buttonClass)) ? $buttonClass : ' btn btn-default btn-sm ';
        return $this->linkField($buttonName, $urlWithParameters, $buttonClass);
    }

    public function imageField(string $label, string $imageUrlWithParameters, int $maxWidth = 50, int $maxHeight = 50)
    {
        $callback = function($data) use ($imageUrlWithParameters, $maxWidth, $maxHeight) {
            $url = Listing::fillUrlParameters($imageUrlWithParameters, $data);
            $maxWidthStyle = ($maxWidth > 0 ? ";max-width:" . $maxWidth . "px" : "");
            $maxHeightStyle = ($maxHeight > 0 ? ";max-height:" . $maxHeight . "px" : "");
            return '<img src="' . $url . '" style="' . $maxWidthStyle . $maxHeightStyle . '" />';
        };

        return $this->customField($label, $callback);
    }

    public function bladeField(string $label, string $bladeViewPath, array $aditionalParameters = [])
    {
        $callback = function($data) use ($label, $bladeViewPath, $aditionalParameters) {
            return view($bladeViewPath, compact('data', 'label', 'aditionalParameters'));
        };

        return $this->customField($label, $callback);
    }

    public function flagField(string $name, string $label)
    {
        $callback = function($data) use ($name) {
            if (!isset($data->$name)) {
                return 'ERRO';
            }
            return '<a href="javascript:;" class="flagItem '
                    . ($data->$name == 1 ? 'flag-on' : 'flag-off')
                    .' " data-field="' . $name . '">'
                    . $data->$name
                    . '</a>';
        };

        return $this->customField($label, $callback, [], $name, 'flag');
    }


}