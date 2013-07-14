<?php

namespace iStudio\Loggers;

use iStudio\BattleResult;
use iStudio\Interfaces\BattleLogger;
use iStudio\Interfaces\Unit;

/**
 * The CLI logger is intended for use on the command line.
 * It outputs one message per second, so it can take a while
 * to finish, but gives the "game" a more turn-based impression.
 *
 * Class LoggerCli
 * @package iStudio
 */
class LoggerCli implements BattleLogger
{

    /** @var array */
    protected $aPrefixes = array(
        BattleLogger::TYPE_HIT => 'Hit',
        BattleLogger::TYPE_MISS => 'Miss',
        BattleLogger::TYPE_DEATH => 'Death',
        BattleLogger::TYPE_MOVE => 'Move',
        BattleLogger::TYPE_INSANE => 'Insanity',
    );

    /** @var array  */
    protected $aColors = array(
        BattleLogger::TYPE_HIT => '0;32m',
        BattleLogger::TYPE_MISS => '0;31m',
        BattleLogger::TYPE_DEATH => '0;33m',
        BattleLogger::TYPE_MOVE => '0;37m',
        BattleLogger::TYPE_INSANE => '0;36m'
    );

    /**
     * Logs a battle result
     * @param BattleResult $oResult
     * @return $this
     */
    function logResult(BattleResult $oResult)
    {

        $sMessage = "\033[".$this->aColors[$oResult->type].' '.$this->aPrefixes[$oResult->type].'! ';
        $sMessage .= $oResult->attacker.' ';
        switch ($oResult->type) {
            case (BattleLogger::TYPE_HIT) :
                $sMessage .= $oResult->message.'. '.ucfirst($oResult->defender);
                $sMessage .= ' takes '.$oResult->amount.' damage.';
                break;
            case (BattleLogger::TYPE_MISS) :
                $sMessage .= $oResult->message.'. '.ucfirst($oResult->defender);
                $sMessage .= ' is safe.';
                break;
            case (BattleLogger::TYPE_DEATH) :
                $sMessage .= 'causes '.$oResult->amount.' damage and '.$oResult->message.' '.ucfirst($oResult->defender).'!!';
                break;
            default:
                break;
        }

        print $sMessage."\033[".$this->aColors[$oResult->type]." \033[1;37m".PHP_EOL;
        usleep(500000);
        return $this;
    }

    /**
     * Logs a misc message
     * @param string $sMessage
     * @return BattleLogger
     */
    function logOther($sMessage) {
        print $sMessage.PHP_EOL;
        usleep(500000);
        return $this;
    }

    /**
     * Logs an array of passed results, automatically picking the proper method
     * @param $aResults
     * @return $this
     */
    function logMultiple(array $aResults) {
        foreach ($aResults as $oResult) {
            if (is_string($oResult)) {
                $this->logOther($oResult);
            } else if ($oResult instanceof BattleResult) {
                $this->logResult($oResult);
            }
        }
        return $this;
    }

}