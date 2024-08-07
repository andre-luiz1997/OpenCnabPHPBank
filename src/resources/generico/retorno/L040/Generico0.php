<?php
/*
 * CnabPHPBank - Geração de arquivos de remessa e retorno em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Ciatec.net
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace CnabPHPBank\resources\generico\retorno\L040;

use CnabPHPBank\RegistroRetAbstract;
use CnabPHPBank\RetornoAbstract;
use Exception;

class Generico0 extends RegistroRetAbstract
{

	protected $counter;

	public function inserirDetalhe($data)
	{
		$class = 'CnabPHPBank\resources\\B' . RetornoAbstract::$banco . '\retorno\\' . RetornoAbstract::$layout . '\Registro1';
		$this->children[] = new $class($data);
	}

	protected function set_situacao_arquivo($value)
	{
		log_message('debug', 'A função ' . __FUNCTION__ . ' foi chamada por ' . debug_backtrace()[1]['function'] . ' na instância ' . get_class($this));
		$this->data['situacao_arquivo'] = ($value == 'T') ? "RETORNO-TESTE" : "RETORNO-PRODUCAO";
	}

	protected function set_data_geracao($value)
	{
		log_message('debug', 'A função ' . __FUNCTION__ . ' foi chamada por ' . debug_backtrace()[1]['function'] . ' na instância ' . get_class($this));
		$this->data['data_geracao'] = date('Y-m-d');
	}

	protected function set_hora_geracao($value)
	{
		log_message('debug', 'A função ' . __FUNCTION__ . ' foi chamada por ' . debug_backtrace()[1]['function'] . ' na instância ' . get_class($this));
		$this->data['hora_geracao'] = date('His');
	}

	protected function set_tipo_inscricao($value)
	{
		if ($value == 1 || $value == 2) {
			$this->data['tipo_inscricao'] = $value;
		} else {
			throw new Exception("O tipo de incrição deve ser 1  para CPF e 2 para CNPJ, o valor informado foi:" . $value);
		}
	}

	protected function set_numero_inscricao($value)
	{
		$this->data['numero_inscricao'] = str_ireplace(array('.', '/', '-'), array(''), $value);
	}

	protected function set_convenio($value)
	{
		$this->data['convenio'] = RetornoAbstract::$entryData['codigo_beneficiario'] . RetornoAbstract::$entryData['codigo_beneficiario_dv'];
	}

	public function get_numero_registro()
	{
		return null;
	}

	public function get_counter()
	{
		$this->counter++;
		return $this->counter;
	}

	public function getRegistros($lote = 1)
	{
		$lote = $this->children[$lote - 1];
		return $lote->getChilds();
	}
}
?>