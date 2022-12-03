<?php 

include "connect.php";
session_start();
ob_start();

if ($_POST["updateprofile"]) {
	
	$nickname = $_POST["nickname"];
	$isim = $_POST["name"];
	$surname = $_POST["surname"];
	$password = md5($_POST["password"]);

	$profile = $db->prepare("SELECT * FROM kayit WHERE u_id=? and u_password=?");
	$profile->execute(array($_SESSION["id"],$password));
	$sonuc = $profile->rowcount();
	
	if ($sonuc == 0 ) {
		header("location:../index.php?hata=sifre");
		exit();
	}

	$veriler = $profile->fetch(PDO::FETCH_ASSOC);
	$eski_resim = substr($veriler["u_resim"],6);

	if ($_FILES["image"]["size"] > 0) {
		if ($_FILES['image']['size']>1048576) {
			Header("Location:../index.php?hata=boyut");
			exit();
		}

		/*Resim varsa burasi*/
		$name = $_FILES["image"]["name"];
		$tmp_name = $_FILES["image"]["tmp_name"];
		$uzanti = pathinfo($name,PATHINFO_EXTENSION);
		$size = $_FILES["image"]["size"];
		$rastgele = "images/".uniqid("IMG-").".".$uzanti;
		move_uploaded_file($tmp_name, $rastgele); /* yeni resim yükleme işlemi*/
		unlink($eski_resim); /*eski resim siliniyor*/

		/*Yeni resim ismi veritabanına yazılıyor*/
		$yeni = $db->prepare("UPDATE kayit SET u_nickname=?,u_name=?,u_surname=?,u_resim=? WHERE u_id=?");
		$yeni->execute(array($nickname,$isim,$surname,"stack/".$rastgele, $_SESSION["id"]));

		header("location:../");
		exit();

	} else {
		/*Resim yoksa burasi*/
		$yeni = $db->prepare("UPDATE kayit SET u_nickname=?,u_name=?,u_surname=? WHERE u_id=?");
		$yeni->execute(array($nickname,$isim,$surname, $_SESSION["id"]));

		header("location:../");
		exit();
	}
}





?>