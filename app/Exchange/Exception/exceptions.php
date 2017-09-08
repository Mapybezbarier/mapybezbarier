<?php

namespace MP\Exchange\Exception;

use MP\Exception\LogicException;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ParseException extends LogicException
{

}

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ExportException extends LogicException
{

}

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ValidationException extends LogicException
{

}

/**
 * Vyjimka pro zpracovnai chyb pri stahovani dat z externich zdroju
 */
class DownloadException extends LogicException
{

}
