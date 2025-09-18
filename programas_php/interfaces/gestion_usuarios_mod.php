<?php

// PROGRAMA DE MENU ADMINISTRADORES
include "conexion.php";

                                                 
session_start();
if ($_SESSION["autenticado"] != "SIx3")
    {
      header('Location: index.php?mensaje=3');
    }
else
    {      
        $mysqli = new mysqli($host, $user, $pw, $db);
  	    $sqlusu = "SELECT * from tipo_usuario where id='1'"; //CONSULTA EL TIPO DE USUARIO CON ID=1, ADMINISTRADOR
        $resultusu = $mysqli->query($sqlusu);
        $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
  	    $desc_tipo_usuario = $rowusu[1];
        if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario)
          header('Location: index.php?mensaje=4');
    }
?>


    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 	Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
     <html>
       <head>
          <link rel="stylesheet" href="css/estilos_virtual.css" 			type="text/css">
           <title> Gesti&oacute;n Usuarios Modif - MERCURY - Puntos de Venta		
		Application</title>
        </head>
       <body>
        <table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
    	   <tr>
       
           <td valign="top" align=left>
               <img src="img/panal.jpg" border=0 width=350 height=80> 
           </td>
           <td valign="top" align=right>
              <font FACE="arial" SIZE=2 color="#000000"> <b><u><?php  echo "Nombre Usuario</u>:   ".$_SESSION["nombre_usuario"];?> </b></font><br>
              <font FACE="arial" SIZE=2 color="#000000"> <b><u><?php  echo "Tipo Usuario</u>:   ".$_SESSION["tipo_usuario"];?> </b></font><br>  
              <font FACE="arial" SIZE=2 color="#00FFFF"> <b><u> <a href="cerrar_sesion.php"> Cerrar Sesion </a></u></b></font>  

           </td>
	     </tr>
<?php
include "menu_admin.php";

if ((isset($_POST["enviado"])))
  {
   //echo "grabar cambios modificaci�n";
   $id_usu_enc = $_POST["id_usu"];
   $nombre_usuario = $_POST["nombre_usuario"];
   $nombre_usuario = str_replace("�","n",$nombre_usuario);
   $nombre_usuario = str_replace("�","N",$nombre_usuario);
   $num_id = $_POST["num_id"];
   $tipo_usuario = $_POST["tipo_usuario"];
   $direccion = $_POST["direccion"];
   $activo = $_POST["activo"];
   $password = $_POST["password"];
   $id_tarjeta = $_POST["id_tarjeta"];
   $login = $_POST["login"];
   
   $mysqli = new mysqli($host, $user, $pw, $db);
	 $sqlu1 = "UPDATE usuarios set nombre_completo='$nombre_usuario' where id='$id_usu_enc'"; 
   $resultsqlu1 = $mysqli->query($sqlu1);
	 $sqlu2 = "UPDATE usuarios set login='$login' where id='$id_usu_enc'"; 
   $resultsqlu2 = $mysqli->query($sqlu2);
   $sqlu3 = "UPDATE usuarios set identificacion='$num_id' where id='$id_usu_enc'"; 
   $resultsqlu3 = $mysqli->query($sqlu3);
   $sqlu4 = "UPDATE usuarios set tipo_usuario='$tipo_usuario' where id='$id_usu_enc'"; 
   $resultsqlu4 = $mysqli->query($sqlu4);
   $sqlu5 = "UPDATE usuarios set direccion='$direccion' where id='$id_usu_enc'"; 
   $resultsqlu5 = $mysqli->query($sqlu5);
   $sqlu6 = "UPDATE usuarios set id_tarjeta='$id_tarjeta' where id='$id_usu_enc'"; 
   $resultsqlu6 = $mysqli->query($sqlu6);
   $sqlu7 = "UPDATE usuarios set activo='$activo' where id='$id_usu_enc'"; 
   $resultsqlu7 = $mysqli->query($sqlu7);
   if ($password != "")
     {
     $password_enc = md5($password);
     $sqlu9 = "UPDATE usuarios set passwd='$password_enc' where id='$id_usu_enc'"; 
     $resultsqlu9 = $mysqli->query($sqlu9);
     }
     
   
   if (($resultsqlu1 == 1) && ($resultsqlu2 == 1) && ($resultsqlu3 == 1) && ($resultsqlu4 == 1) && 
       ($resultsqlu5 == 1) && ($resultsqlu6 == 1) && ($resultsqlu7 == 1)) 
         header('Location: gestion_usuarios.php?mensaje=1');
   else
         header('Location: gestion_usuarios.php?mensaje=2');
   
}

else

{

// Consulta el nombre y dem�s datos del usuario a modificar
   $id_usu_enc = $_GET["id_usu"];
   $mysqli = new mysqli($host, $user, $pw, $db);
   $sqlenc = "SELECT * from usuarios";
   $resultenc = $mysqli->query($sqlenc);
   while($rowenc = $resultenc->fetch_array(MYSQLI_NUM))
    {  
      $id_usu  = $rowenc[0];
      if (md5($id_usu) == $id_usu_enc)
        $id_usu_enc = $id_usu;
    }
   $sql1 = "SELECT * from usuarios where id='$id_usu_enc'";
   $result1 = $mysqli->query($sql1);
   $row1 = $result1->fetch_array(MYSQLI_NUM);
   $nombre_usuario  = $row1[1];
   $tipo_usuario  = $row1[7];
   $num_id = $row1[2];
   $direccion= $row1[3];
   $activo= $row1[9];
   $id_tarjeta= $row1[8];
   $login= $row1[5];


   if ($activo == 1)
      $desc_activo = "S (Activo)";
   else
      $desc_activo = "N (Inactivo)";
      
   $sql3 = "SELECT * from tipo_usuario where id='$tipo_usuario'";
   $result3 = $mysqli->query($sql3);
   $row3 = $result3->fetch_array(MYSQLI_NUM);
   $desc_tipo_usuario = $row3[1];

   ?>
	
	   <tr valign="top">
                <td width="50%" height="20%" align="left" 				
                    bgcolor="#FFFFFF" class="_espacio_celdas" 					
                    style="color: #FFFFFF; 
			             font-weight: bold">
			    <font FACE="arial" SIZE=2 color="#000044"> <b><h1>Gesti&oacute;n Usuarios </h1>  Modificando Usuario: <u><?php echo $nombre_usuario; ?></u></b></font>  
          

		       </td>
	          <td width="50%" height="20%" align="right" 				
                    bgcolor="#FFFFFF" class="_espacio_celdas" 					
                    style="color: #FFFFFF; 
			             font-weight: bold">
			  <img src="img/gestion_usuarios.jpg" border=0 width=115 height=115>    
		       </td>
		     </tr>
   	     <tr>
                  <td colspan=2 width="25%" height="20%" align="left" 				
                    bgcolor="#FFFFFF" class="_espacio_celdas" 					
                    style="color: #FFFFFF; 
			             font-weight: bold">

                   <form method=POST action="gestion_usuarios_mod.php">
                   <table width=50% border=1 align=center>
			    <tr>	
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>Nombre Usuario</b></font>  
				</td>	
				<td bgcolor="#EEEEEE" align=center> 
				  <input type="text" name=nombre_usuario value="<?php echo $nombre_usuario; ?>" required>  
				</td>	
	     </tr>
	     <tr>
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>N&uacute;mero Id</b></font>  
				</td> 	
				<td bgcolor="#EEEEEE" align=center> 
				  <input type="number" name=num_id value="<?php echo $num_id; ?>" required>  
				</td>	
			     </tr>
			     <tr>
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>Tipo Usuario</b></font>  
				</td>
				<td bgcolor="#EEEEEE" align=center> 
			    <select name=tipo_usuario required> 
           <option value="<?php echo $tipo_usuario; ?>"> <?php echo $desc_tipo_usuario; ?></option>  
           <?php 	
           $sql6 = "SELECT * from tipo_usuario";
           $result6 = $mysqli->query($sql6);
           while($row6 = $result6->fetch_array(MYSQLI_NUM))
            {
              $tipo_usuario_con = $row6[0];
              $desc_tipo_usuario_con = $row6[1];
              if ($tipo_usuario_con != $tipo_usuario)
               {
           ?>   
              <option value="<?php echo $tipo_usuario_con; ?>"> <?php echo $desc_tipo_usuario_con; ?></option>  
           <?php
               }
            }
           ?>
          </select>
				</td>	
			  </tr>
			  <tr>
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>Usuario</b></font>  
				</td>
				<td bgcolor="#EEEEEE" align=center> 
				  <input type="text" name=login value="<?php echo $login; ?>" required>  
				</td>	
			  </tr>

			  <tr>
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>Clave (dejar en blanco para no cambiar)</b></font>  
				</td>
				<td bgcolor="#EEEEEE" align=center> 
				  <input type="password" name=password value="">  
				</td>	
			  </tr>
			  <tr>
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>Direcci�n</b></font>  
				</td>
				<td bgcolor="#EEEEEE" align=center> 
				  <input type="text" name=direccion value="<?php echo $direccion; ?>" required>  
				</td>	
			  </tr>
			  <tr>
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>Id Tarjeta</b></font>  
				</td>
				<td bgcolor="#EEEEEE" align=center> 
				  <input type="number" name=id_tarjeta min=1 max=9 value="<?php echo $id_tarjeta; ?>" required>  
				</td>	
			  </tr>
	     <tr>
				<td bgcolor="#D1E8FF" align=center> 
				  <font FACE="arial" SIZE=2 color="#000044"> <b>Activo (S/N)</b></font>  
				</td>
				<td bgcolor="#EEEEEE" align=center> 
<select name=activo required> 
           <option value="<?php echo $activo; ?>"> <?php echo $desc_activo; ?></option>  
           <?php
           $activo_con = 1;
           $desc_activo_con = "S (Activo)";
           if ($activo_con != $activo)
               {
           ?>   
              <option value="<?php echo $activo_con; ?>"> <?php echo $desc_activo_con; ?></option>  
           <?php
               }
           else
               {    
           ?>
              <option value="0"> N (Inactivo)</option>  
           <?php
               }
           ?> 
          </select>
				</td>	
	     </tr>
      </table>
         </br>
         <input type="hidden" value="S" name="enviado">
         <input type="hidden" value="<?php echo $id_usu_enc; ?>" name="id_usu">
         <table width=50% align=center border=0>
           <tr>  
             <td width=50%></td>                                                                       
             <td align=center><input style="background-color: #DBA926" type=submit color= blue value="Modificar" name="Modificar">
                  </form> 
             </td>  
             <td align=left>
                  <form method=POST action="gestion_usuarios.php">                   
                  <input style="background-color: #EEEEEE" type=submit color= blue value="Volver" name="Volver">              
                  </form> 
             </td>  
           </tr>
                   </table>
                  </form> 
<br><br><hr>
                  </td>
                </tr>  

<?php
 }
?>

        </table>
        
       </body>
      </html>


   
