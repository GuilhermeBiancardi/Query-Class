<?php

class Query {

    private $ip       = "";
    private $database = "";
    private $user     = "";
    private $pass     = "";
    private $conn;

    /**
     * __construct
     *
     * @param  mixed $ip
     * @param  mixed $database
     * @param  mixed $user
     * @param  mixed $pass
     *
     * @return void
     */
    public function __construct($ip = "", $database = "", $user = "", $pass = "") {
        if (is_array($ip)) {
            $this->ip = $ip["ip"];
            $this->database = $ip["database"];
            $this->user = $ip["user"];
            $this->pass = $ip["pass"];
        } else {
            if ($ip && $database && $user && $pass) {
                $this->ip = $ip;
                $this->database = $database;
                $this->user = $user;
                $this->pass = $pass;
            }
        }
    }

    /**
     * AbreConexao
     *
     * @return void
     */
    private function AbreConexao() {
        $this->conn = mysqli_connect($this->ip, $this->user, $this->pass);
        mysqli_select_db($this->conn, $this->database);
    }

    /**
     * FechaConexao
     *
     * @return void
     */
    private function FechaConexao() {
        mysqli_close($this->conn);
    }

    // String para teste: '<script>alert("\nIñtërnâtiônàlizætiøn\t");</script>'
    /**
     * AntiSqlInjection
     *
     * @param  mixed $str
     *
     * @return void
     */
    public function AntiSqlInjection($str) {
        $str = filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_ENCODE_HIGH);
        return $str;
    }

    /**
     * Atualizar
     *
     * @param  mixed $sql
     *
     * @return void
     */
    public function Atualizar($sql) {
        $this->AbreConexao();
        $result = mysqli_query($this->conn, $sql);
        $saida  = false;
        if ($result) {
            $saida = true;
        } else {
            mysqli_affected_rows($this->conn);
            $numero_erro = mysqli_errno($this->conn);
            $texto_erro  = mysqli_error($this->conn);
            if ($numero_erro > 0) {
                echo $numero_erro . ": " . $texto_erro . "<br/>" . $sql;
                return false;
            }
        }
        $this->FechaConexao();
        return $saida;
    }

    /**
     * Inserir
     *
     * @param  mixed $sql
     *
     * @return void
     */
    public function Inserir($sql) {
        $this->AbreConexao();
        mysqli_query($this->conn, $sql);
        $novo_codigo = mysqli_insert_id($this->conn);
        $numero_erro = mysqli_errno($this->conn);
        $texto_erro  = mysqli_error($this->conn);
        $this->FechaConexao();

        if ($numero_erro > 0) {
            echo $numero_erro . ": " . $texto_erro . "<br/>" . $sql;
            return false;
        }
        return $novo_codigo;
    }

    /**
     * Select
     *
     * @param  mixed $sql
     *
     * @return void
     */
    public function Select($sql) {
        $this->AbreConexao();
        $select = mysqli_query($this->conn, $sql);
        $data   = array();
        $i      = 0;
        if ($select) {
            while ($rs_arr = mysqli_fetch_array($select)) {
                foreach ($rs_arr as $key => $value) {
                    if (!is_numeric($key)) {
                        $data[$i][$key] = stripslashes($value);
                    }
                }
                $i++;
            }
        }
        $this->FechaConexao();
        if (@$data) {
            return $data;
        } else {
            return false;
        }
    }
}
