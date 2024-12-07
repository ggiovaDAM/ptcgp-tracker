<?php
    /**
     * Creates a conexion to the database especified in <b>$xmlFile</b> and returns it.
     * Will default to an error page (```resources/pdo/error_database.php```) if:
     * <ul>
     *   <li>If <b>```$xmlFile```</b> doens't exist.</li>
     *   <li>If <b>```$xsdFile```</b> doens't exist.</li>
     *   <li>If <b>```$xmlFile```</b> doens't have the proper XML format.</li>
     *   <li>If <b>```$xmlFile```</b> doesn't follow the <b>$xsdFile</b>'s structure.</li>
     * </ul>
     * 
     * Use: ```require __DIR__ . "/resources/pdo/db.php";```
     * 
     * @param string $xmlFile File that has the information for the conexion to the database.
     * @param string $xsdFile Verifies and validates that the <b>$xmlFile</b> is defined properly. 
     * @return PDO|bool Returns the conexion to the database if successful. Will send to an error page if the conexion to the database fails.
     */
    function connectToDatabase(String $xmlFile, String $xsdFile = __DIR__ . "/server_config_validate.xsd"): PDO | bool {
        if (@file_exists($xmlFile) === false) {
            failDatabase("¡Error!", "El archivo <b>$xmlFile</b> no fue encontrado, por favor asegúrese que el fichero exista y que la dirección este escrita correctamenta!");
        }

        if (@file_exists($xsdFile) === false) {
            failDatabase("¡Error!", "El archivo <b>$xsdFile</b> no fue encontrado, por favor asegúrese que el fichero exista y que la dirección este escrita correctamenta!");
        }

        $xml = @simplexml_load_file($xmlFile);

        if ($xml === false) {
            failDatabase("¡Error!", "El archivo <b>$xmlFile</b> no tiene el formato correcto para un XML, por favor asegúrese que el fichero esté correctamente formateado!");
        }

        $dom = new DOMDocument();
        $dom->loadXML($xml->asXML());

        if (@$dom->schemaValidate($xsdFile) === false) {
            failDatabase("¡Error!", "El archivo <b>$xmlFile</b> no es validado correctamente con el <b>XSD</b> ($xsdFile)!");
        }

        $dbtype   = (string) $xml->dbtype;
        $dbname   = (string) $xml->dbname;
        $host     = (string) $xml->host;
        $port     = (string) $xml->port;
        $user     = (string) $xml->user;
        $password = (string) $xml->password;

        try {
            $conexion = "$dbtype:dbname=$dbname;host=$host" . (isset($port) ? ":$port" : "");
            return new PDO($conexion, $user, $password, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"]);
        } catch (PDOException $e) {
            failDatabase("¡Error!", "Error al iniciar la base de datos!", "<b>Motivo:</b>" . $e->getMessage());
        }

        return false;
    }

    /**
     * En caso de error, enviará al usuario a una página de error, mostrando el motivo.
     * 
     * @param string $title Títlo que tendrá.
     * @param string[] $parrafos Todos los párrafos que mostrará el error.
     * @return void No devolverá nada.
     */
    function failDatabase(String $title, String ...$parrafos): void {
        $result = "<h1>$title</h1>";
        foreach ($parrafos as $parrafo) { 
            $result .= "<p>$parrafo</p>";
        }
        @session_start();
        $_SESSION["DATABASE_CONNECTION_ERROR"] = $result;
        @header("Location: ../../backend/pdo/error_database.php");
    }