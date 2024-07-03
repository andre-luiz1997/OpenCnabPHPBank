<?php
/*
 * CnabPHPBank - Gera��o de arquivos de remessa e retorno em PHP
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

class Generico3 extends RegistroRetAbstract
{
	public function get_R3U()
	{
		return $this->children[0];
	}

	protected function set_codigo_lote($value)
	{
		$this->data['codigo_lote'] = RetornoAbstract::$loteCounter;
	}

	protected function set_numero_registro($value)
	{
		$lote = RetornoAbstract::getLote(RetornoAbstract::$loteCounter);
		$this->data['numero_registro'] = $lote->get_counter();
	}


	protected function set_numero_inscricao($value)
	{
		$this->data['numero_inscricao'] = str_ireplace(array('.', '/', '-'), array(''), $value);
	}

	protected function set_tipo_inscricao($value)
	{
		$this->data['tipo_inscricao'] = $value;
	}

	protected function set_agencia($value)
	{
		$this->data['agencia'] = RetornoAbstract::$entryData['agencia'];
	}

	protected function set_agencia_dv($value)
	{
		$this->data['agencia_dv'] = RetornoAbstract::$entryData['agencia_dv'];
	}

	protected function set_conta($value)
	{
		$this->data['conta'] = RetornoAbstract::$entryData['conta'];
	}

	protected function set_conta_dv($value)
	{
		$this->data['conta_dv'] = RetornoAbstract::$entryData['conta_dv'];
	}

	protected function set_seu_numero($value)
	{
		if ($this->data['nosso_numero'] == 0 && $value == '') {
			throw new Exception('O campo "seu_numero" é obrigatório, na sua falta usarei o nosso numero, porém esse também não foi inserido!!!');
		} else {
			$this->data['seu_numero'] = $value != ' ' ? $value : $this->data['nosso_numero'];
		}
	}
}