<?php

namespace iLUB\Plugins\CleanUpSessions\Jobs;

use Exception;
use ilCronJob;
use iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess;
use ilCleanUpSessionsPlugin;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


/**
 * Class RunSync
 *
 * @package iLUB\Plugins\CleanUpSessions\Jobs
 */
class RunSync extends AbstractJob {

	/**
	 * @var logger
	 */
	protected $logger;

	protected $dic;

	/**
	 * @return string
	 */
	public function getId() {
		return get_class($this);
	}


	/**
	 * @return bool
	 */
	public function hasAutoActivation() {
		return true;
	}


	/**
	 * @return bool
	 */
	public function hasFlexibleSchedule() {
		return true;
	}


	/**
	 * @return int
	 */
	public function getDefaultScheduleType() {
		return ilCronJob::SCHEDULE_TYPE_DAILY;
	}


	/**
	 * @return null
	 */
	public function getDefaultScheduleValue() {
		return 1;
	}

    /**
     * @return \ilCronJobResult
     */
    public function getJobResult(){
	    return new \ilCronJobResult();
    }

    /**
     * @return CleanUpSessionsDBAccess
     * @throws Exception
     */
    public function getDBAccess(){
	    return new CleanUpSessionsDBAccess();
    }
	/**
	 * @return \ilCronJobResult
	 * @throws
	 */
	public function run() {

		$jobResult = $this->getJobResult();

		try {

			$tc = $this->getDBAccess();
			$tc->removeAnonymousSessionsOlderThanExpirationThreshold();

			$jobResult->setStatus($jobResult::STATUS_OK);
			$jobResult->setMessage("Everything worked fine.");
			return $jobResult;
		} catch (Exception $e) {
			$jobResult->setStatus($jobResult::STATUS_CRASHED);
			$jobResult->setMessage("There was an error.");
			return $jobResult;
		}
	}
}
