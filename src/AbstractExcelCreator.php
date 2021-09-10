<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;


/**
 * Class AbstractExcelCreator
 */
abstract class AbstractExcelCreator
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * ExcelGenerator constructor.
     */
    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    /**
     * @param $title
     * @param null $description
     */
    protected function setHeaders($title, $description = NULL)
    {
        if ($description === NULL) {
            $description = "$title document for Office 2005 XLSX !";
        }

        $this->spreadsheet->getProperties()->setCreator("GIFI-LOC")
            ->setLastModifiedBy("PHP project - PW Starter - kit")
            ->setTitle($title)
            ->setDescription($description);
    }


    /**
     * @param string $filename
     * @throws Exception
     */
    protected function save($filename = 'upload/test.xls')
    {
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xls');

        $writer->save($filename);
    }

    /**
     * @throws Exception
     */
    protected function stream()
    {
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xls');
        header('Content-Disposition: attachment; filename="'. urlencode('test.xls').'"');
        return $writer->save('php://output');
    }

}