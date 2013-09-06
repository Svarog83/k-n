<?

class Mail
{
	static private $_sLastMessage;

	function __construct()
    {
    }

	private static function _send( $sToEmails, $sSubject, $sMessage, $sHeaders )
    {
        self::$_sLastMessage = $sMessage;
        $sSubject = $sSubject;
        $mail = @mail( $sToEmails, $sSubject, $sMessage, $sHeaders );
        return $mail;
    }

	/////////////
    ////////////////////////////////////
    ////////////////////////////////////
    // Функция высылки почты в кодировке КОИ8 и чистом тексте

    public static function mail_koi_text( $to, $subject, $message, $from = '', $to_name = '', $from_name = '' )
    {
        $admin_mail = Request::getVar( 'admin_mail', 'global' );

        if( !$from )
            $from = $admin_mail;

        if( !$from_name )
            $from_name = $from;

        if( is_array( $to ) )
            $to = implode( ',', $to );

        $message    = convert_cyr_string( $message, "w", "k");
        $subject    = convert_cyr_string( $subject, "w", "k");//Странно, но пользователь получает абрукадабру
        $from_name  = convert_cyr_string( $from_name, "w", "k");
        $to_name    = convert_cyr_string( $to_name, "w", "k");

        $message    = strip_tags( trim( $message ) );
        $subject    = strip_tags( trim( $subject ) );
        $from_name  = strip_tags( trim( $from_name ) );
        $to_name    = strip_tags( trim( $to_name ) );
        $from       = strip_tags( trim( $from ) );
        $to         = strip_tags( trim( $to ) );

        $from_name = $from_name;

        $headers    = "From: ". $from_name ." <". $from .">\n";
        $headers    .= "X-Sender: <". $from .">\n";
        $headers    .= "X-Mailer: PHP-mailsender\n"; // mailer

        if( $from ==  $admin_mail )
            $headers    .= "Return-Path: ". $admin_mail ."\n"; // Return path for errors
        else
            $headers    .= "Return-Path: <". $from .">\n"; // Return path for errors

        $headers .= "Content-Type: text/plain; charset=\"koi8-r\"\nContent-Transfer-Encoding: 8bit"; // Mime type

        $priority = Request::getVar( 'priority', 'global' );

        if( $priority )
            $headers .= "\nX-MSMail-Priority: ". $priority;

        $mailed = self::_send( $to, $subject, $message, $headers );

        return $mailed;
    }


}