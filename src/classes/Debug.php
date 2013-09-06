<?
class Debug
{
    var $_display_errors        = '1';    // Отображать ошибки
    var $_error_reporting       = E_ALL;  // Отображать ошибки
    
    var $_max_execution_time    = 3600;     // Лимит времяни 60 * 60 * 24 = 86400; 60 * 30 = 1800; 60 * 60 = 3600
    var $_memory_limit          = '1024M';  // Лимит памяти
    
    static private $_time       = FALSE;    // Время старта
    
    static private $_aLine      = array();    // Время старта
    
    
    function set_limite()
    {
        ini_set( 'memory_limit', $this -> _memory_limit );
        ini_set( 'max_execution_time', $this -> _max_execution_time );
    }
    
    function set_error_report()
    {
        ini_set( 'display_errors', $this -> _display_errors );
        ini_set( 'error_reporting', $this -> _error_reporting);
    }
    
     public static function reset_time()
     {
         self::$_time = FALSE;
     }
    
    public static function time( $flagEcho = FALSE, $__LINE__ = FALSE, $__FILE__ = FALSE )
    {
        if( self::$_time === FALSE )
            self::$_time = microtime( TRUE );
        
        $timeDelta =  microtime( TRUE ) - self::$_time;
        
        if( $flagEcho )
             $timeDelta = self::prnt_r( "\t" . $timeDelta, $__LINE__, $__FILE__, $flagEcho );
        return $timeDelta;
    }
    
    public static function prnt_r( $mData, $__LINE__ = FALSE, $__FILE__ = FALSE, $flagEcho = FALSE )
    {
        $sOut = "\n";
        if( $__FILE__ !== FALSE )
            $sOut .= "$__FILE__";
        if( $__LINE__ !== FALSE )
            self::$_aLine[ $__LINE__ ] += 1; 
            $sOut .= "[$__LINE__][" . self::$_aLine[ $__LINE__ ] . "]";
        
        $sOut .= print_r( $mData, 1 ) . "";
        if( $flagEcho === TRUE )
            echo $sOut;
        return $sOut;
    }
    
    public static function my_error_handler( $errType, $errStr, $errFile = FALSE, $errLine = FALSE, $display = TRUE )
    {
        $sOut = '';
        switch ($errType)
        {
            case 'FATAL':
                $sTmp =  "<font color=red><br><b>$errType!!!</b> $errStr<br>\n".
                         ( ( $errFile || $errLine ) ? "  Fatal error in line " . $errLine . " of file " . $errFile . "<br>" : "" ).
                         "Aborting...<br></font>\n";
                if( $display )
                    echo $sTmp;
                exit();
                break;
            case 'ERROR':
                $sOut = "<b>$errType!!!</b> $errStr<br>\n";
                break;
            case 'WARNING':
                $sTmp =  "<font color=red><br><b>$errType!!!</b></font><br> $errStr<br>\n".
                         ( ( $errFile || $errLine ) ? " error in line " . $errLine . " of file " . $errFile . "<br>" : "" ).
                         "\n";
                $sOut = $sTmp;
                break;
            default:
                $sOut = "Unkown error type: [$errType] $errStr<br>\n";
                break;
        }
        if( $display )
            echo $sOut;
        return $sOut;
    }
    
    public static function in_file( $text, $path = '', $file = FALSE, $mode = "a+" )
    {
        if  ( !$path )
	        $path = $_SERVER['DOCUMENT_ROOT'] . "/tmp";
	    
        $file = $file ? $file : date( "y.m.d" ).".php";
        $filename = $path."/".$file;
        
        $fp = @fopen( $filename, $mode);
        if( $fp )
            fwrite( $fp, $text );
        @fclose( $fp );
    }

}
