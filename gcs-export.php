<?php
/** Export File Tree to TSV for import into Google Cloud Storage **/
function getDirContents($dir, &$results = array()){
		if( !file_exists( $dir ) ){ return []; }
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = str_replace( '"', '', $path );
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
        }
    }
    return $results;
}

function gcsTSV( $directory, $output = "output.tsv" ) {
    $results = getDirContents( $directory );
    $fp = fopen( $output , 'w');
    fwrite( $fp, "TsvHttpData-1.0\n");
    foreach( $results as $result ){
      $file = str_replace( '/home4/mps/public_html', 'http://monumental.services', $result );
      $file = str_replace( ' ', '%20', htmlspecialchars( $file ) );
      fwrite( $fp, $file."\t".filesize( $result )."\t".base64_encode( md5_file( $result, true ) )."\n" );
    }
    fclose($fp);
}
