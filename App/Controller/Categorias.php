<?php
session_start();

use App\Core\Controller;

class Categorias extends Controller{

    public function index(){

        $categoriaModel = $this->model("Categoria");

        $categorias = $categoriaModel->listarTodas();

        echo json_encode($categorias, JSON_UNESCAPED_UNICODE);
    }

    public function find($id){

        $categoriaModel = $this->model("Categoria");

        $categoria = $categoriaModel->buscarPorId($id);

        if($categoria){
            echo json_encode($categoria, JSON_UNESCAPED_UNICODE);
        }else{
            http_response_code(404);
            echo json_encode(["erro" => "Categoria não encontrada"]);
        }
    }

    public function store(){

        //pegando o corpo da requisição, retona uma string
        $json = file_get_contents("php://input");

        //convertendo a string em objeto
        $novaCategoria = json_decode($json);

        //instanciando o model
        $categoriaModel = $this->model("Categoria");

        //atribuindo a descricao ao model
        $categoriaModel->descricao = $novaCategoria->descricao;

        //chamando o método inserir do model
        $categoriaModel = $categoriaModel->inserir();

        //verificando se deu certo
        if($categoriaModel){
            //se deu certo, retornar a categoria inserida
            http_response_code(201);
            echo json_encode($categoriaModel, JSON_UNESCAPED_UNICODE);

        }else{
            //se deu errado, mudar status code para 500 e retornar mensagem de erro
            http_response_code(500);
            echo json_encode(["erro" => "Problemas ao inserir categoria"]);
        }

    }

    public function update($id){

        $categoriaEditar = $this->getRequestBody();

        $categoriaModel = $this->model("Categoria");

        $categoriaModel = $categoriaModel->buscarPorId($id);

        if(!$categoriaModel){
            http_response_code(404);
            echo json_encode(["erro" => "Categoria não encontrada"]);
            exit;
        }

        $categoriaModel->descricao = $categoriaEditar->descricao;

        if($categoriaModel->atualizar()){
            http_response_code(204);
        }else{
            http_response_code(500);
            echo json_encode(["erro" => "Problemas ao editar categoria"]);
        }

    }

    public function delete($id){

        $categoriaModel = $this->model("Categoria");

        $categoriaModel = $categoriaModel->buscarPorId($id);

        if(!$categoriaModel){
            http_response_code(404);
            echo json_encode(["erro" => "Categoria não encontrada"]);
            exit;
        }

        $produtos = $categoriaModel->getProdutos();

        if($produtos != []){
            http_response_code(400); //bad request
            echo json_encode(["erro" => "Não pôde excluir, categoria com produtos cadastrados"]);
            exit;
        }

        if($categoriaModel->deletar()){
            http_response_code(204);
        }else{
            http_response_code(500);
            echo json_encode(["erro" => "Problemas ao excluir categoria"]);
        }
    }
}