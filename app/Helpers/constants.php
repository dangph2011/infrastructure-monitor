<?php

// define value type of item (items table)

define("ITEM_VALUE_TYPE_FLOAT", 0);
define("ITEM_VALUE_TYPE_CHARACTER", 1);
define("ITEM_VALUE_TYPE_LOG", 2);
define("ITEM_VALUE_TYPE_UNSIGNED", 3);
define("ITEM_VALUE_TYPE_TEXT", 4);

//define graph type (graph table)
define("GRAPH_TYPE_NORMAL", 0);
define("GRAPH_TYPE_STACKED", 1);
define("GRAPH_TYPE_PIE", 2);
define("GRAPH_TYPE_EXPLODED", 3);

define('GRAPH_ITEM_SIMPLE',			0);
define('GRAPH_ITEM_SUM',			2);

//Trigger state
define('TRIGGER_STATE_NORMAL',	0);
define('TRIGGER_STATE_UNKNOWN',	1);

//Trigger Value
define('TRIGGER_VALUE_FALSE',	0);
define('TRIGGER_VALUE_TRUE',	1);

//Trigger options
define('TRIGGERS_OPTION_RECENT_PROBLEM',	1);
define('TRIGGERS_OPTION_ALL',				2);
define('TRIGGERS_OPTION_IN_PROBLEM',		3);

//Trigger severity
define('TRIGGER_SEVERITY_NOT_CLASSIFIED',	0);
define('TRIGGER_SEVERITY_INFORMATION',		1);
define('TRIGGER_SEVERITY_WARNING',			2);
define('TRIGGER_SEVERITY_AVERAGE',			3);
define('TRIGGER_SEVERITY_HIGH',				4);
define('TRIGGER_SEVERITY_DISASTER',			5);
define('TRIGGER_SEVERITY_COUNT',			6);

//Round option
define('ZBX_UNITS_ROUNDOFF_THRESHOLD',		0.01);
define('ZBX_UNITS_ROUNDOFF_UPPER_LIMIT',	2);
define('ZBX_UNITS_ROUNDOFF_MIDDLE_LIMIT',	4);
define('ZBX_UNITS_ROUNDOFF_LOWER_LIMIT',	6);

//Value by second
define('SEC_PER_MIN',			60);
define('SEC_PER_HOUR',			3600);
define('SEC_PER_DAY',			86400);
define('SEC_PER_WEEK',			604800);
define('SEC_PER_MONTH',			2592000);
define('SEC_PER_YEAR',			31536000);

define('SCALE_RATIO', 0.8);
