<?php

declare(strict_types=1);

namespace Psl\Filesystem;

use Psl;

/**
 * Open a file handle for read and write.
 *
 * @throws Psl\Exception\InvariantViolationException If $filename points to a non-file node, or it not writeable.
 * @throws Exception\RuntimeException If unable to create $filename when it does not exist.
 */
function open_file_read_write(string $filename, string $write_mode = WriteMode::OPEN_OR_CREATE): ReadWriteFileHandleInterface
{
  if ($write_mode === WriteMode::MUST_CREATE && exists($filename)) {
    Psl\invariant_violation('$filename already exists.');
  }

  $creating = $write_mode === WriteMode::OPEN_OR_CREATE ||
    $write_mode === WriteMode::MUST_CREATE;
  if (!$creating && !exists($filename)) {
    Psl\invariant_violation('$filename does not exist.');
  }

  if (
    (!$creating ||
      ($write_mode === WriteMode::OPEN_OR_CREATE && exists($filename))) &&
    !is_writeable($filename)
  ) {
    Psl\invariant_violation('$filename is not writeable.');
  }

  if ($creating && !exists($filename)) {
    create_file($filename);
  }

  return Internal\open_file($filename, 'r' . $write_mode . '+');
}