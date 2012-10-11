<?php

//! @file Lint.php
//! @brief This file contains the Lint class.
//! @details
//! @author Filippo F. Fadda


//! @brief TODO
class Lint {

  private $sourceCode;


  //! @brief TODO
  public function loadFromFile($fileName) {
    $this->sourceCode = "";

    if (file_exists($fileName)) {
      $fd = fopen($fileName, "r");

      if (is_resource($fd)) {
        while (!feof($fd))
          $this->sourceCode .= fgets($fd);
      }
      else
        throw new \Exception("Cannot open the file.");
    }
    else
      throw new \Exception("\$fileName doesn't exist.");
  }


  //! @brief TODO
  public function loadFromString($str) {
    $this->sourceCode = "";

    if (is_string($str))
      $this->sourceCode = $str;
    else
      throw new \Exception("\$str must be a string.");
  }


  //! @brief TODO
  public function checkSyntax($addTags = FALSE) {
    if ($addTags)
      // We add the PHP tags, else the lint ignores the code. The PHP command line option -r doesn't work.
      $this->sourceCode = "<?php ".$this->sourceCode." ?>";

    // Try to create a temporary physical file. The function 'proc_open' doesn't allow to use a memory file.
    if ($fd = fopen("php://temp", "r+")) {
      fputs($fd, $this->sourceCode); // Writes the message body.
      // We don't need to flush because we call rewind.
      rewind($fd); // Sets the pointer to the beginning of the file stream.

      $dspec = array(
        $fd,
        1 => array('pipe', 'w'), // stdout
        2 => array('pipe', 'w'), // stderr
      );

      $proc = proc_open(PHP_BINARY." -l", $dspec, $pipes);

      if (is_resource($proc)) {
        // Reads the stdout output.
        $output = "";
        while (!feof($pipes[1])) {
          $output .= fgets($pipes[1]);
        }

        // Reads the stderr output.
        $error = "";
        while (!feof($pipes[2])) {
          $error .= fgets($pipes[2]);
        }

        // Free all resources.
        fclose($fd);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proc);

        if (preg_match("/\ANo syntax errors/", $output) === 0) {
          $pattern = array("/\APHP Parse error:  /",
            "/in - /",
            "/\z -\n/");

          $error = ucfirst(preg_replace($pattern, "", $error));

          throw new \Exception($error);
        }
      }
      else
        throw new \Exception("Cannot execute the 'PHP -l' command.");
    }
    else
      throw new \Exception("Cannot create the temporary file with the source code.");
  }

}
