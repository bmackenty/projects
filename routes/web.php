<?php 

$request_method = $_SERVER['REQUEST_METHOD'];
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = filter_var($request, FILTER_SANITIZE_URL);

switch ($request) {
    case '/':
        require __DIR__ . '/../app/Controllers/HomeController.php';
        (new HomeController())->index();
        break;
    case '/about':
        require __DIR__ . '/../app/Views/about.php';
        break;
    case '/login':
        if ($request_method === 'GET') {
            require __DIR__ . '/../app/Views/login.php';
        } elseif ($request_method === 'POST') {
            require __DIR__ . '/../app/Controllers/LoginController.php';
        }
        break;
    case '/register':
        if ($request_method === 'GET') {
            require __DIR__ . '/../app/Views/register.php';
        } elseif ($request_method === 'POST') {
            require __DIR__ . '/../app/Controllers/RegisterController.php';
        }
        break;
    case '/logout':
        require __DIR__ . '/../app/Controllers/LogoutController.php';
        break;

    // New project routes
    case '/projects':
        if ($request_method === 'GET') {
            require __DIR__ . '/../app/Controllers/ProjectController.php';
            (new ProjectController())->index();
        }
        break;
    case '/projects/create':
        if ($request_method === 'GET') {
            require __DIR__ . '/../app/Controllers/ProjectController.php';
            (new ProjectController())->create();
        } elseif ($request_method === 'POST') {
            require __DIR__ . '/../app/Controllers/ProjectController.php';
            (new ProjectController())->create();
        }
        break;
    case (preg_match('/^\/projects\/edit\/(\d+)$/', $request, $matches) ? true : false):
        $id = $matches[1];
        if ($request_method === 'GET') {
            require __DIR__ . '/../app/Controllers/ProjectController.php';
            (new ProjectController())->edit($id);
        } elseif ($request_method === 'POST') {
            require __DIR__ . '/../app/Controllers/ProjectController.php';
            (new ProjectController())->edit($id);
        }
        break;

    case (preg_match('/^\/tasks\/(\d+)\/upload$/', $request, $matches) ? true : false):
        $task_id = $matches[1];
        require __DIR__ . '/../app/Controllers/TaskController.php';
        (new TaskController())->uploadFile($task_id);
        break;

    case (preg_match('/^\/projects\/(\d+)\/tasks\/create$/', $request, $matches) ? true : false):
        $project_id = $matches[1];
        require __DIR__ . '/../app/Controllers/TaskController.php';
        (new TaskController())->create($project_id);
        break;

    case (preg_match('/^\/tasks\/edit\/(\d+)$/', $request, $matches) ? true : false):
        $task_id = $matches[1];
        require __DIR__ . '/../app/Controllers/TaskController.php';
        (new TaskController())->edit($task_id);
        break;

    case (preg_match('/^\/projects\/view\/(\d+)$/', $request, $matches) ? true : false):
        $id = $matches[1];
        require __DIR__ . '/../app/Controllers/ProjectController.php';
        (new ProjectController())->view($id);
        break;

    case '/dashboard':
        require __DIR__ . '/../app/Controllers/ProjectController.php';
        (new ProjectController())->dashboard();
        break;

    case (preg_match('/^\/tasks\/(\d+)\/comment$/', $request, $matches) ? true : false):
        $task_id = $matches[1];
        require __DIR__ . '/../app/Controllers/TaskController.php';
        (new TaskController())->addComment($task_id);
        break;

    case (preg_match('/^\/tasks\/view\/(\d+)$/', $request, $matches) ? true : false):
        $task_id = $matches[1];
        require __DIR__ . '/../app/Controllers/TaskController.php';
        (new TaskController())->view($task_id);
        break;

    case (preg_match('/^\/uploads\/(.+)$/', $request, $matches) ? true : false):
        $filename = $matches[1];
        require __DIR__ . '/../app/Controllers/TaskController.php';
        (new TaskController())->serveFile($filename);
        break;

    case (preg_match('/^\/tasks\/upload\/(\d+)\/rename$/', $request, $matches) ? true : false):
        $upload_id = $matches[1];
        require __DIR__ . '/../app/Controllers/TaskController.php';
        (new TaskController())->renameUpload($upload_id);
        break;

    default:
        require __DIR__ . '/../app/Views/404.php';
        break;
}