<?php

//! @file Lint.php
//! @brief This file contains the Lint class.
//! @details
//! @author Filippo F. Fadda


namespace Lint;


//! @brief Lint is a wrapper to 'PHP -l' command.
final class Lint {

  private static function checkSyntax($sourceCode, $addTags = FALSE) {
    if ($addTags)
      // We add the PHP tags, else the lint ignores the code. The PHP command line option -r doesn't work.
      $sourceCode = "<?php ".$sourceCode." ?>";

    // Try to create a temporary physical file. The function 'proc_open' doesn't allow to use a memory file.
    if ($fd = fopen("php://temp", "r+")) {
      fputs($fd, $sourceCode); // We don't need to flush because we call rewind.
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


  //! @brief Makes the syntax check of the specified file. If an error occurs, generate an exception.
  //! @warning File source code must be included in PHP tags.
  //! @param[in] string $fileName The file name you want check.
  //! @exception Exception <c>Message: <i>Cannot open the file $fileName.</i></c>
  //! @exception Exception <c>Message: <i>\$fileName doesn't exist.</i></c>
  public static function checkSourceFile($fileName) {
    if (file_exists($fileName)) {
      $fd = fopen($fileName, "r");

      if (is_resource($fd)) {
        $sourceCode = "";

        while (!feof($fd))
          $sourceCode .= fgets($fd);

        self::checkSyntax($sourceCode);
      }
      else
        throw new \Exception("Cannot open the file $fileName.");
    }
    else
      throw new \Exception("\$fileName doesn't exist.");
  }


  //! @brief Makes the syntax check of the given source code. If an error occurs, generate an exception.
  //! @exception Exception <c>Message: <i>\$str must be a string.</i></c>
  //! @param[in] string $str The source code.
  //! @param[in] boolean $addTags (optional) Tells if you want add PHP tags to the source code, because PHP lint needs
  //! them or it will raise an exception.
  public static function checkSourceCode($str, $addTags = TRUE) {
    if (is_string($str))
      self::checkSyntax($str, $addTags);
    else
      throw new \Exception("\$str must be a string.");
  }

}