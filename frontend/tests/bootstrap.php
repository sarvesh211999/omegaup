<?php

// Older (~Xenial) PHP Unit compatibility hack.
// We should be able to remove this once we upgrade PHPUnit and/or PHP.
namespace PHPUnit\Framework {
    if (!class_exists('\\PHPUnit\\Framework\\TestCase')) {
        class TestCase extends \PHPUnit_Framework_TestCase {
        }
    }
}

namespace {
    define('IS_TEST', true);

    // Set timezone to UTC
    date_default_timezone_set('UTC');

    // Set remote address to localhost.
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    define('OMEGAUP_ROOT', __DIR__ . '/..');

    // Load test specific config globals
    // Do not panic if the test-specific override file is not present.
    @include_once(OMEGAUP_ROOT . '/tests/test_config.php');
    require_once(OMEGAUP_ROOT . '/tests/test_config.default.php');
    require_once(OMEGAUP_ROOT . '/server/config.default.php');

    // Load api caller
    require_once(OMEGAUP_ROOT . '/www/api/ApiCaller.php');
    require_once(OMEGAUP_ROOT . '/tests/controllers/ApiCallerMock.php');

    // Load test utils
    require_once(OMEGAUP_ROOT . '/tests/controllers/OmegaupTestCase.php');
    require_once(OMEGAUP_ROOT . '/tests/common/Utils.php');

    // Load Factories
    require_once(OMEGAUP_ROOT . '/tests/factories/ProblemsFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/ContestsFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/ClarificationsFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/UserFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/CoursesFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/RunsFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/GroupsFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/SchoolsFactory.php');
    require_once(OMEGAUP_ROOT . '/tests/factories/QualityNominationFactory.php');

    require_once(OMEGAUP_ROOT . '/server/libs/Time.php');

    // Clean previous log
    Utils::CleanLog();

    // Clean problems and runs path
    Utils::CleanPath(GRADE_PATH);
    Utils::CleanPath(IMAGES_PATH);
    Utils::CleanPath(PROBLEMS_GIT_PATH);
    Utils::CleanPath(RUNS_PATH);
    Utils::CleanPath(TEMPLATES_PATH);

    for ($i = 0; $i < 256; $i++) {
        mkdir(RUNS_PATH . sprintf('/%02x', $i), 0775, true);
        mkdir(GRADE_PATH . sprintf('/%02x', $i), 0775, true);
    }

    // Clean DB
    Utils::CleanupDB();

    // Clean APC cache
    Cache::clearCacheForTesting();

    // Create a test default user for manual UI operations
    UserController::$sendEmailOnVerify = false;
    $admin = UserFactory::createUser(new UserParams([
        'username' => 'admintest',
        'password' => 'testtesttest',
    ]));
    ACLsDAO::save(new ACLs([
        'acl_id' => Authorization::SYSTEM_ACL,
        'owner_id' => $admin->user_id,
    ]));
    UserRolesDAO::save(new UserRoles([
        'user_id' => $admin->user_id,
        'role_id' => Authorization::ADMIN_ROLE,
        'acl_id' => Authorization::SYSTEM_ACL,
    ]));
    UserFactory::createUser(new UserParams([
        'username' => 'test',
        'password' => 'testtesttest',
    ]));
    UserController::$sendEmailOnVerify = true;

    // Globally disable run wait gap.
    RunController::$defaultSubmissionGap = 0;

    QualityNominationFactory::initQualityReviewers();
    QualityNominationFactory::initTags();

    // Mock time
    $current_time = time();
    Time::setTimeForTesting($current_time);
    $conn->EXECUTE('SET TIMESTAMP = ' . $current_time);

    Grader::setInstanceForTesting(new NoOpGrader());
}
