<?php
/**
 * Classe Contato - Gerencia contatos no banco de dados
 * Arquivo: src/classes/Contato.php
 */

class Contato {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    // Salvar novo contato
    public function salvar($dados) {
        $sql = "INSERT INTO contatos (nome, email, telefone, assunto, mensagem) 
                VALUES (:nome, :email, :telefone, :assunto, :mensagem)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($dados);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao salvar contato: " . $e->getMessage());
        }
    }
    
    // Obter todos os contatos
    public function obterTodos() {
        $sql = "SELECT * FROM contatos ORDER BY data_envio DESC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter contatos: " . $e->getMessage());
        }
    }
    
    // Obter contatos não lidos
    public function obterNaoLidos() {
        $sql = "SELECT * FROM contatos WHERE lido = 0 ORDER BY data_envio DESC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter contatos: " . $e->getMessage());
        }
    }
    
    // Marcar como lido
    public function marcarComoLido($id) {
        $sql = "UPDATE contatos SET lido = 1 WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar contato: " . $e->getMessage());
        }
    }
    
    // Deletar contato
    public function deletar($id) {
        $sql = "DELETE FROM contatos WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar contato: " . $e->getMessage());
        }
    }
    
    // Obter um contato específico
    public function obterPorId($id) {
        $sql = "SELECT * FROM contatos WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter contato: " . $e->getMessage());
        }
    }
}
?>
