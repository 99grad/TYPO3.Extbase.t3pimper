<?php

namespace NNGrad\T3pimper\Utilities;

/**
 * Example condition
 */
class ConditionUtility {

        /**
         * Evaluate condition
         *
         * @param mixed $conditionParameters
         * @return bool
         */
        public function matchCondition($cmd) {
        	return \NNGrad\T3pimper\Utilities\SettingsUtility::isEnabledInConf($cmd);
        }
}