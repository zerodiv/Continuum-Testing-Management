-- truncate all tables that might have development data in them
TRUNCATE account;

-- CTM_Test tables
TRUNCATE test;
TRUNCATE test_baseurl;
TRUNCATE test_description;
TRUNCATE test_html_source;

-- CTM_Test_Command
TRUNCATE test_command;
TRUNCATE test_command_target;
TRUNCATE test_command_value;

-- CTM_Test_Browser
TRUNCATE test_browser;

-- CTM_Test_Folder
TRUNCATE test_folder;

-- CTM_Test_Machine
TRUNCATE test_machine;
TRUNCATE test_machine_browser;

-- CTM_Test_Param
TRUNCATE test_param_library;
TRUNCATE test_param_library_default_value;
TRUNCATE test_param_library_description;

-- CTM_Test_Run
TRUNCATE test_run;
TRUNCATE test_run_baseurl;
TRUNCATE test_run_command;
TRUNCATE test_run_command_target;
TRUNCATE test_run_command_value;

-- Possibly reusable, but erring on the side of caution here.
TRUNCATE test_selenium_command;

-- CTM_Test_Suite
TRUNCATE test_suite;
TRUNCATE test_suite_baseurl;
TRUNCATE test_suite_description;

-- CTM_Test_Suite_Plan
TRUNCATE test_suite_plan;
