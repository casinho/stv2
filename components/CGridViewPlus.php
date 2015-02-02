<?php
 
Yii::import('bootstrap.widgets.TbGridView');
 
class CGridViewPlus extends TbGridView {
 
    public $addingHeaders = array();
 
    public function renderTableHeader() {
        if (!empty($this->addingHeaders))
            $this->multiRowHeader();
 
        parent::renderTableHeader();
    }
 
    protected function multiRowHeader() {
        echo CHtml::openTag('thead') . "\n";
        foreach ($this->addingHeaders as $row) {
            $this->addHeaderRow($row);
        }
        echo CHtml::closeTag('thead') . "\n";
    }
 
    protected function addHeaderRow($row) {
        // add a single header row
        echo CHtml::openTag('tr') . "\n";
        foreach ($row as $header => $options) {
            echo CHtml::openTag('th', $options);
            echo $header;
            echo CHtml::closeTag('th');
        }
        echo CHtml::closeTag('tr') . "\n";
    }
}
