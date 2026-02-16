<?php
/**
 * Classe Agendamento - Gerencia agendamentos no banco de dados
 * Arquivo: src/classes/Agendamento.php
 */

class Agendamento {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    // Salvar novo agendamento
    public function salvar($dados) {
        $sql = "INSERT INTO agendamentos (nome, telefone, email, profissional, data, hora, estilo, descricao) 
                VALUES (:nome, :telefone, :email, :profissional, :data, :hora, :estilo, :descricao)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($dados);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao salvar agendamento: " . $e->getMessage());
        }
    }
    
    // Obter todos os agendamentos
    public function obterTodos() {
        $sql = "SELECT * FROM agendamentos ORDER BY data DESC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter agendamentos: " . $e->getMessage());
        }
    }
    
    // Obter agendamento por ID
    public function obterPorId($id) {
        $sql = "SELECT * FROM agendamentos WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter agendamento: " . $e->getMessage());
        }
    }
    
    // Obter agendamentos por profissional
    public function obterPorProfissional($profissional) {
        $sql = "SELECT * FROM agendamentos WHERE profissional = :profissional ORDER BY data DESC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':profissional' => $profissional]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter agendamentos: " . $e->getMessage());
        }
    }
    
    // Atualizar agendamento
    public function atualizar($id, $dados) {
        $sql = "UPDATE agendamentos SET 
                nome = :nome, 
                telefone = :telefone, 
                email = :email, 
                profissional = :profissional, 
                data = :data, 
                hora = :hora, 
                estilo = :estilo, 
                descricao = :descricao,
                status = :status 
                WHERE id = :id";
        
        $dados['id'] = $id;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($dados);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar agendamento: " . $e->getMessage());
        }
    }
    
    // Deletar agendamento
    public function deletar($id) {
        $sql = "DELETE FROM agendamentos WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar agendamento: " . $e->getMessage());
        }
    }
    
    // Verificar disponibilidade
    public function verificarDisponibilidade($profissional, $data, $hora) {
        $sql = "SELECT COUNT(*) as total FROM agendamentos 
                WHERE profissional = :profissional 
                AND data = :data 
                AND hora = :hora 
                AND status != 'cancelado'";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':profissional' => $profissional,
                ':data' => $data,
                ':hora' => $hora
            ]);
            $resultado = $stmt->fetch();
            return $resultado['total'] === 0; // Retorna true se está disponível
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar disponibilidade: " . $e->getMessage());
        }
    }
}
?>
