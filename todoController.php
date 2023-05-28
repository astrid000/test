<?php
class TodoController
{
    private $conectar;
    private $Connection;

    public function __construct()
    {
        require_once __DIR__ . "/../core/Conectar.php";
        require_once __DIR__ . "/../models/todo.php";
        $this->conectar = new Conectar();
        $this->Connection = $this->conectar->Connection();
    }

    public function run($accion)
    {
        switch ($accion) {
            case "index":
                $this->index();
                break;
            case "alta":
                $this->crear();
                break;
            case "detalle":
                $this->detalle();
                break;
            case "actualizar":
                $this->actualizar();
                break;
            default:
                $this->index();
                break;
        }
    }

    public function index()
    {
        $todo = new Todo($this->Connection);
        $todos = $todo->getAll();

        $todosPerPage = 5;
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $totalTodos = count($todos);
        $totalPages = ceil($totalTodos / $todosPerPage);

        $startIndex = ($currentPage - 1) * $todosPerPage;
        $pagedTodos = array_slice($todos, $startIndex, $todosPerPage);

        $searchTitle = isset($_GET['search_title']) ? $_GET['search_title'] : '';

        if (!empty($searchTitle)) {
            $filteredTodos = array_filter($todos, function ($todo) use ($searchTitle) {
                return stripos($todo['Title'], $searchTitle) !== false;
            });

            $filteredTotalTodos = count($filteredTodos);
            $filteredTotalPages = ceil($filteredTotalTodos / $todosPerPage);

            $startIndex = ($currentPage - 1) * $todosPerPage;
            $pagedTodos = array_slice($filteredTodos, $startIndex, $todosPerPage);

            $this->view("index", array(
                "filteredPagedTodos" => $pagedTodos,
                "titulo" => "PHP MVC",
                "totalPages" => $filteredTotalPages, // 确保包含此变量
                "currentPage" => $currentPage,
                "searchTitle" => $searchTitle
            ));
            
        } else {
            $this->view("index", array(
                "filteredPagedTodos" => $pagedTodos,
                "titulo" => "PHP MVC",
                "totalPages" => $totalPages,
                "currentPage" => $currentPage,
                "searchTitle" => ''
            ));
        }
    }

    // ...

    public function detalle()
    {
        $modelo = new Todo($this->Connection);
        $todo = $modelo->getById($_GET["id"]);
        $this->view("detalle", array(
            "todo" => $todo,
            "titulo" => "Detalle Todo"
        ));
    }

    public function crear()
    {
        if (isset($_POST["Title"])) {
            $todo = new Todo($this->Connection);
            $todo->setTitle($_POST["Title"]);
            $todo->setType($_POST["Type"]);
            $todo->setContent($_POST["Content"]);
            $todo->setDate($_POST["Date"]);
            $save = $todo->save();
        }
        header('Location: index.php');
    }

    public function actualizar()
    {
        if (isset($_POST["id"])) {
            $todo = new Todo($this->Connection);
            $todo->setId($_POST["id"]);
            $todo->setTitle($_POST["Title"]);
            $todo->setType($_POST["Type"]);
            $todo->setContent($_POST["Content"]);
            $todo->setDate($_POST["Date"]);
            $save = $todo->update();
        }
        header('Location: index.php');
    }

    public function view($vista, $datos)
    {
        $data = $datos;
        require_once __DIR__ . "/../views/" . $vista . "View.php";
        exit;
    }
}
