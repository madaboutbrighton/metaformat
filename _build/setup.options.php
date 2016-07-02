<?php

$output = '';

switch ($options[xPDOTransport::PACKAGE_ACTION])
{
  case xPDOTransport::ACTION_INSTALL:
  case xPDOTransport::ACTION_UPGRADE:
  case xPDOTransport::ACTION_UNINSTALL:
    break;
}

return $output;