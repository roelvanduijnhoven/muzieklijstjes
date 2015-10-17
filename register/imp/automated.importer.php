<?php

error_reporting(E_ALL);
ini_set('memory_limit', '1600M');
set_time_limit(0);
set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes');

require_once "PHPExcel/IOFactory.php";

new Bootstrap();

class Bootstrap
{
    public function __construct()
    {
        $this->log('Automated importer started');
        
        $this->log('Create tab seperated files');
        $this->log('---');
        
        /*$this->log('List descriptions');
        $data = $this->parseListDescription();
        $this->log('> parsed excel file');
        $this->writeTabFile($data, 'temp/lijstenB.txt');
        $this->log('> wrote tab seperated file');*/
        
        $this->log('Album & Review information');
        $data = $this->parseAlbumInformation();
        $this->log('> parsed excel file');
        $this->writeTabFile($data, 'temp/algemeen.txt');
        $this->log('> wrote tab seperated file');
    }
    
    /**
     * @return array
     */
    protected function parseListDescription()
    {
        $sheet = $this->getParsedSheet('excel/LegendaMuzieklijstjes.xlsx');
        
        $rowNum = 0;
        $dataArray = array();
        foreach( $sheet->getRowIterator() as $row )
        {
            $rowNum++;
            if( $rowNum == 1)
                continue;
                
            $cells = $this->getRowCellValues($row);
            $dataArray[] = $cells;
        }
        
        return $dataArray;
    }
    
    /**
     * @return array
     */
    protected function parseAlbumInformation()
    {
        $sheet = $this->getParsedSheet('excel/BestandJuni2009.xlsx');
        
        $rowNum = 0;
        $dataArray = array();
        
        $rowIterator = $sheet->getRowIterator();
        foreach( $rowIterator as $row )
        {
            $rowNum++;
            if( $rowNum == 1)
                continue;
            if( $rowNum == 1000)
                break;
            $cells = $this->getRowCellValues($row);
            $dataArray[] = $cells;
        }
        $rowIterator->__destruct();
        
        return $dataArray;
    }    
    
    /**
     * @param array $rows
     * @param string $fileName
     */
    protected function writeTabFile( array $rows, $fileName )
    {
        $count = 0;
        $contents = '';
        foreach( $rows as $row )
        {
            $count++;
            $contents .= implode( "\t", $row );
            if( $count !== count($rows) )
                $contents .= PHP_EOL;
        }
        
        file_put_contents($fileName, $contents);
    }
    
    /**
     * @param string $fileName
     * @return PHPExcel_Worksheet
     */
    private function getParsedSheet( $fileName )
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        
        $excel = $objReader->load($fileName);
        $objWorksheet = $excel->getActiveSheet();

        return $objWorksheet;
    }
   
    /**
     * @param PHPExcel_Worksheet_Row $row
     * @return array
     */
    private function getRowCellValues( PHPExcel_Worksheet_Row $row )
    {
        $cells = array();
        
        for( $i = 0; $i < 26; $i++ )
            $cells[$i] = '';
        
        $rowIterator = $row->getCellIterator();
        foreach( $rowIterator as $cell )
        {
            if( $cell === NULL )
                continue;

            $cells[$this->getIndexForColumn($cell->getColumn())] = $cell->getValue();
        }
        $rowIterator->__destruct();

        return $cells;            
    }
    
    /**
     * @var array
     */
    private $letterToIndex = NULL;
    
    /**
     * @param char $char
     * @return integer
     */
    private function getIndexForColumn( $char )
    {
        if( $this->letterToIndex == NULL )
        {
            $this->letterToIndex = array();
            for( $i = 0; $i < 26; $i++ )
                $this->letterToIndex[ chr(65 + $i) ] = $i;
        }

        return $this->letterToIndex[$char];
    }    
    
    /**
     * @param string $message
     * @return void
     */
    private function log( $message )
    {
        echo $message . PHP_EOL;
    }
}








