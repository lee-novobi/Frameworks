<?php
	/**
	 * @author Quang Minh
	 * @copyright VNG JSC
     * @build date 04-03-2011
     * @version 1.0
     * @description: Store common function for website
     * @PHPVersion > 5.0
	 */
	/* --------------------------------------------------------------------- */
	
	require_once('mongo_conn.php');
	/**
	 * Phan tich du lieu can them vao
	 * @param array $data
	 * @return string
	 **/

	function compile_db_insert_string($data) {
		$field_names  = "";
        $field_values = "";
        foreach ($data as $k => $v){
            $v = addslashes( $v );
            $field_names  .= $k.",";
            $field_values .= "'".$v."',";
        }
        $field_names  = preg_replace( "/,$/" , "" , $field_names  );
        $field_values = preg_replace( "/,$/" , "" , $field_values );
        return array( 'FIELD_NAMES'  => $field_names,
                      'FIELD_VALUES' => $field_values,
                    );
    }

	/* --------------------------------------------------------------------- */

	/**
	 * Phan tich du lieu can cap nhat
	 * @param array $data
	 * @return string
	 **/

	function compile_db_update_string($data) {
        $return_string = "";
        foreach ($data as $k => $v){
            $v = addslashes($v );
            $return_string .= $k . "='".$v."',";
        }
        $return_string = preg_replace( "/,$/" , "" , $return_string );
        return $return_string;
 	}

	/* --------------------------------------------------------------------- */
	/**
	 * Them moi du lieu trong mot table
	 * @param array $data
	 * @param string $table
	 * @return Object
	 **/
	function InsertAll($data,$table){
		global $oMAConn, $oMADB;
		$oClt = new MongoCollection($oMADB, $table);
		$oRs = $oClt->insert($data);
	}

	/* --------------------------------------------------------------------- */

	/**
	 * Cap nhat du lieu trong mot table
	 * @param array $data
	 * @param string $table
	 * @param string $where
	 * @return Object
	 **/
	function UpdateAll($data,$table,$where){
		global $oMAConn, $oMADB;
		$oClt = new MongoCollection($oMADB, $table);
		$oRs = $oClt->update($where, $data);
	}
	
	/* --------------------------------------------------------------------- */

	/**
	 * Cap nhat du lieu trong mot table
	 * @param array $data
	 * @param string $table
	 * @param string $where
	 * @return Object
	 **/
	function FindAll($table,$where){
		global $oMAConn, $oMADB;
		$oClt = new MongoCollection($oMADB, $table);
		$oRs = $oClt->find($where);
		return $oRs;
	}
	
	/* --------------------------------------------------------------------- */

	/**
	 * Cap nhat du lieu trong mot table
	 * @param array $data
	 * @param string $table
	 * @param string $where
	 * @return Object
	 **/
	function CountAll($table,$where){
		global $oMAConn, $oMADB;
		$oClt = new MongoCollection($oMADB, $table);
		$oRs = $oClt->count($where);
		var_dump($where);
		return $oRs;
	}

	/* --------------------------------------------------------------------- */

	/**
	 * Xoa du lieu trong mot table
	 * @param string $table
	 * @param string $where
	 * @return Object
	 **/

	function DeleteAll($table,$where="",$connection){
	    global $dbconn, $config, $smarty, $id_user, $lang,$name_tem;
	    $query = "DELETE FROM ".$table;
	    if ( trim($where) != "" ){
	        $query .= " WHERE ".$where;
	    }
        if(MODE_DEBUG == true){
            echo $query;
            echo "<br>";
        }
	    $rest = mysql_query($query,$connection);
        return $rest;
	}

	/* --------------------------------------------------------------------- */

	/**
	 * In mot mang du lieu
	 * @param array $array
	 * @return string
	 **/

	function _DebugArray($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
?>