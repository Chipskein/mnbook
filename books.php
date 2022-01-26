#!/usr/bin/php
<?php
error_reporting(0);
class ConsoleTable
{
    const HEADER_INDEX = -1;
    const HR = 'HR';

    /** @var array Array of table data */
    protected $data = array();
    /** @var boolean Border shown or not */
    protected $border = true;
    /** @var boolean All borders shown or not */
    protected $allBorders = false;
    /** @var integer Table padding */
    protected $padding = 1;
    /** @var integer Table left margin */
    protected $indent = 0;
    /** @var integer */
    private $rowIndex = -1;
    /** @var array */
    private $columnWidths = array();
    /** @var int */
    private $maxColumnCount = 0;

    /**
     * Adds a column to the table header
     * @param  mixed  Header cell content
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function addHeader($content = '')
    {
        $this->data[self::HEADER_INDEX][] = $content;

        return $this;
    }

    /**
     * Set headers for the columns in one-line
     * @param  array  Array of header cell content
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function setHeaders(array $content)
    {
        $this->data[self::HEADER_INDEX] = $content;

        return $this;
    }

    /**
     * Get the row of header
     */
    public function getHeaders()
    {
        return isset($this->data[self::HEADER_INDEX]) ? $this->data[self::HEADER_INDEX] : null;
    }

    /**
     * Adds a row to the table
     * @param  array  $data The row data to add
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function addRow(array $data = null)
    {
        $this->rowIndex++;

        if (is_array($data)) {
            foreach ($data as $col => $content) {
                $this->data[$this->rowIndex][$col] = $content;
            }

            $this->setMaxColumnCount(count($this->data[$this->rowIndex]));
        }

        return $this;
    }

    /**
     * Adds a column to the table
     * @param  mixed    $content The data of the column
     * @param  integer  $col     The column index to populate
     * @param  integer  $row     If starting row is not zero, specify it here
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function addColumn($content, $col = null, $row = null)
    {
        $row = $row === null ? $this->rowIndex : $row;
        if ($col === null) {
            $col = isset($this->data[$row]) ? count($this->data[$row]) : 0;
        }

        $this->data[$row][$col] = $content;
        $this->setMaxColumnCount(count($this->data[$row]));

        return $this;
    }

    /**
     * Show table border
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function showBorder()
    {
        $this->border = true;

        return $this;
    }

    /**
     * Hide table border
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function hideBorder()
    {
        $this->border = false;

        return $this;
    }

    /**
     * Show all table borders
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function showAllBorders()
    {
        $this->showBorder();
        $this->allBorders = true;

        return $this;
    }

    /**
     * Set padding for each cell
     * @param  integer $value The integer value, defaults to 1
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function setPadding($value = 1)
    {
        $this->padding = $value;

        return $this;
    }

    /**
     * Set left indentation for the table
     * @param  integer $value The integer value, defaults to 1
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function setIndent($value = 0)
    {
        $this->indent = $value;

        return $this;
    }

    /**
     * Add horizontal border line
     * @return object LucidFrame\Console\ConsoleTable
     */
    public function addBorderLine()
    {
        $this->rowIndex++;
        $this->data[$this->rowIndex] = self::HR;

        return $this;
    }

    /**
     * Print the table
     * @return void
     */
    public function display()
    {
        echo $this->getTable();
    }

    /**
     * Get the printable table content
     * @return string
     */
    public function getTable()
    {
        $this->calculateColumnWidth();

        $output = $this->border ? $this->getBorderLine() : '';
        foreach ($this->data as $y => $row) {
            if ($row === self::HR) {
                if (!$this->allBorders) {
                    $output .= $this->getBorderLine();
                    unset($this->data[$y]);
                }

                continue;
            }

            if ($y === self::HEADER_INDEX && count($row) < $this->maxColumnCount) {
                $row = $row + array_fill(count($row), $this->maxColumnCount - count($row), ' ');
            }

            foreach ($row as $x => $cell) {
                $output .= $this->getCellOutput($x, $row);
            }
            $output .= PHP_EOL;

            if ($y === self::HEADER_INDEX) {
                $output .= $this->getBorderLine();
            } else {
                if ($this->allBorders) {
                    $output .= $this->getBorderLine();
                }
            }
        }

        if (!$this->allBorders) {
            $output .= $this->border ? $this->getBorderLine() : '';
        }

        if (PHP_SAPI !== 'cli') {
            $output = '<pre>'.$output.'</pre>';
        }

        return $output;
    }

    /**
     * Get the printable border line
     * @return string
     */
    private function getBorderLine()
    {
        $output = '';

        if (isset($this->data[0])) {
            $columnCount = count($this->data[0]);
        } elseif (isset($this->data[self::HEADER_INDEX])) {
            $columnCount = count($this->data[self::HEADER_INDEX]);
        } else {
            return $output;
        }

        for ($col = 0; $col < $columnCount; $col++) {
            $output .= $this->getCellOutput($col);
        }

        if ($this->border) {
            $output .= '+';
        }
        $output .= PHP_EOL;

        return $output;
    }

    /**
     * Get the printable cell content
     *
     * @param integer $index The column index
     * @param array   $row   The table row
     * @return string
     */
    private function getCellOutput($index, $row = null)
    {
        $cell       = $row ? $row[$index] : '-';
        $width      = $this->columnWidths[$index];
        $padding    = str_repeat($row ? ' ' : '-', $this->padding);

        $output = '';

        if ($index === 0) {
            $output .= str_repeat(' ', $this->indent);
        }

        if ($this->border) {
            $output .= $row ? '|' : '+';
        }

        $output .= $padding; # left padding
        $cell    = trim(preg_replace('/\s+/', ' ', $cell)); # remove line breaks
        $content = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $cell);
        $delta   = mb_strlen($cell, 'UTF-8') - mb_strlen($content, 'UTF-8');
        $output .= $this->strPadUnicode($cell, $width + $delta, $row ? ' ' : '-'); # cell content
        $output .= $padding; # right padding
        if ($row && $index == count($row) - 1 && $this->border) {
            $output .= $row ? '|' : '+';
        }

        return $output;
    }

    /**
     * Calculate maximum width of each column
     * @return array
     */
    private function calculateColumnWidth()
    {
        foreach ($this->data as $row) {
            if (is_array($row)) {
                foreach ($row as $x => $col) {
                    $content = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $col);
                    if (!isset($this->columnWidths[$x])) {
                        $this->columnWidths[$x] = mb_strlen($content, 'UTF-8');
                    } else {
                        if (mb_strlen($content, 'UTF-8') > $this->columnWidths[$x]) {
                            $this->columnWidths[$x] = mb_strlen($content, 'UTF-8');
                        }
                    }
                }
            }
        }

        return $this->columnWidths;
    }

    /**
     * Multibyte version of str_pad() function
     * @source http://php.net/manual/en/function.str-pad.php
     */
    private function strPadUnicode($str, $padLength, $padString = ' ', $dir = STR_PAD_RIGHT)
    {
        $strLen     = mb_strlen($str, 'UTF-8');
        $padStrLen  = mb_strlen($padString, 'UTF-8');

        if (!$strLen && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
            $strLen = 1;
        }

        if (!$padLength || !$padStrLen || $padLength <= $strLen) {
            return $str;
        }

        $result = null;
        $repeat = ceil($strLen - $padStrLen + $padLength);
        if ($dir == STR_PAD_RIGHT) {
            $result = $str . str_repeat($padString, $repeat);
            $result = mb_substr($result, 0, $padLength, 'UTF-8');
        } elseif ($dir == STR_PAD_LEFT) {
            $result = str_repeat($padString, $repeat) . $str;
            $result = mb_substr($result, -$padLength, null, 'UTF-8');
        } elseif ($dir == STR_PAD_BOTH) {
            $length = ($padLength - $strLen) / 2;
            $repeat = ceil($length / $padStrLen);
            $result = mb_substr(str_repeat($padString, $repeat), 0, floor($length), 'UTF-8')
                . $str
                . mb_substr(str_repeat($padString, $repeat), 0, ceil($length), 'UTF-8');
        }

        return $result;
    }

    /**
     * Set max column count
     * @param int $count The column count
     */
    private function setMaxColumnCount($count)
    {
        if ($count > $this->maxColumnCount) {
            $this->maxColumnCount = $count;
        }
    }
}

class BookController
{
    private $db_connection=null;

    function __construct(){
        $db=new Sqlite3("/home/chipskein/Books/books.db");
        $db->exec("PRAGMA FOREIGN_KEY=ON");
        $this->db_connection=$db;
    }
    function add($bookname){
        $connection=$this->db_connection;
        $id=crc32($bookname);
        $query="INSERT INTO books(identifier,name) VALUES($id,\"$bookname\")";
        $connection->exec($query);
        return $connection->lastInsertRowID();
    }
    function addAllFromDir(){
        foreach (new DirectoryIterator('./') as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->getExtension()=="pdf")
            {
                error_reporting(0);//disabling foreing keys error
                $bookname=$fileInfo->getFilename();
                echo "-------------------------------------------------------------------\n";
                echo "Adding File:$bookname\n";
                $job=$this->add($bookname);
                if($job==0) echo "FILE ALREADY EXISTS!\n";
                else echo "FILE ADDED:$bookname\n";
            }
        }
    }
    function remove($bookname){
        $connection=$this->db_connection;
        $id=crc32($bookname);
        $query="DELETE FROM books WHERE identifier=$id";
        $connection->exec($query);
    }
    function update($bookname,$pages){
        $connection=$this->db_connection;
        $id=crc32($bookname);
        $query="UPDATE books SET last_page=$pages,last_update=CURRENT_TIMESTAMP WHERE identifier=$id";
        $connection->exec($query);
    }
    function create_table(){
        $connection=$this->db_connection;
        $query="
            CREATE TABLE IF NOT EXISTS books(
                identifier integer not null,
                name varchar(400) not null,
                last_page integer default 0,
                register_time default CURRENT_TIMESTAMP,
                last_update default CURRENT_TIMESTAMP,
                PRIMARY KEY(identifier)    
            )
        ";
        $connection->exec($query);
    }
    function getAll(){
        $tbl = new ConsoleTable();
        $connection=$this->db_connection;
        $query="SELECT substr( name, 0,40 ) as name ,last_page,last_update FROM books";
        $result=$connection->query($query);
        $tbl->setHeaders(array('name','last_page','last_update'));

        while ($row = $result->fetchArray()){
            $page=$row["last_page"];
            $date=$row["last_update"];
            $tbl->addRow(array($row["name"],$page,$date));
        }
        echo $tbl->getTable();

    }
    function get($bookname){
        $tbl = new ConsoleTable();
        $connection=$this->db_connection;
        $id=crc32($bookname);
        $query="SELECT substr( name, 0,40 ) as name ,last_page,last_update FROM books WHERE identifier=$id";
        $result=$connection->query($query);
        $tbl->setHeaders(array('name','last_page','last_update'));

        while ($row = $result->fetchArray()){
            $page=$row["last_page"];
            $date=$row["last_update"];
            $tbl->addRow(array($row["name"],$page,$date));
        }
        echo $tbl->getTable();
    }
}
$controller=new BookController();
$operation=(isset($argv[1])) ? $argv[1]:null;
$book=(isset($argv[2])) ? $argv[2]:null;
echo "$book\n";
$page=(isset($argv[3])) ? $argv[3]:null;
switch($operation){
    case "add":
        $controller->add($book);
        break;
    case "addall":
        $controller->addAllFromDir();
        break;
    case "update":
        $controller->update($book,$page);
        break;
    case "delete":
        $controller->delete($book);
        break;
    case "getall":
        $controller->getAll();
        break;
    case "get":
        $controller->get($book);
        break;
    case "help":
        echo "Syntax: mnbook \$(operation) \$(book) \$(page)\n";
        $tbl = new ConsoleTable();
        $tbl->setHeaders(['operation','arg1','arg2']);
        $tbl->addRow(['add','$bookname','NONE']);
        $tbl->addRow(['addall','NONE','NONE']);
        $tbl->addRow(['update','$bookname','$page']);
        $tbl->addRow(['delete','$bookname','NONE']);
        $tbl->addRow(['getall','NONE','NONE']);
        $tbl->addRow(['get','$bookname','NONE']);
        $tbl->addRow(['help','NONE','NONE']);
        echo $tbl->getTable();
        break;
    default:
        echo "Syntax: mnbook \$(operation) \$(book) \$(page)\n";
        $tbl = new ConsoleTable();
        $tbl->setHeaders(['operation','arg1','arg2']);
        $tbl->addRow(['add','$bookname','NONE']);
        $tbl->addRow(['addall','NONE','NONE']);
        $tbl->addRow(['update','$bookname','$page']);
        $tbl->addRow(['delete','$bookname','NONE']);
        $tbl->addRow(['getall','NONE','NONE']);
        $tbl->addRow(['get','$bookname','NONE']);
        $tbl->addRow(['help','NONE','NONE']);
        echo $tbl->getTable();
        break;
}
?>