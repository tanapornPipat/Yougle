<?php
	session_start();
	$connect = mysqli_connect("localhost", "root", "", "perfectrecipe");
	$ID = $_GET['id'];
	
?>
<!DOCTYPE html>
<html>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<head>
		<title>
			<?php
				$temp = "SELECT recipeName FROM recipedata WHERE  recipeID = ".$ID;
				$name = $connect->query($temp);
				$title = $name->fetch_assoc();
				echo $title['recipeName'];
			?>
		</title>
		<link rel = "stylesheet" type = "text/css" href = "css/bootstrap.min.css" />
		<link rel = "stylesheet" type = "text/css" href = "css/bootstrap-theme.min.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
		<link href="https://fonts.googleapis.com/css?family=Arima Madurai" rel="stylesheet">
		<link rel = "stylesheet" type = "text/css" href = "css/style.css" />
		<link rel = "stylesheet" type = "text/css" href = "css/parsley.css" />
		<script src="js/jquery.js"></script>
		<script src="js/validation.min.js"></script>
		<script src="js/scripts.js"></script>
		<script src="js/parsley.min.js"></script>
		<script>
			$(document).ready(function(){

				$("#fav").mouseenter(function(){
					$('#favorite').removeClass("fa fa-star-o");
					$('#favorite').addClass("fa fa-star");
				});
				$("#fav").mouseleave(function(){
					$('#favorite').removeClass("fa fa-star");
					$('#favorite').addClass("fa fa-star-o");
				});

				$("#unfav").mouseenter(function(){
					$('#unfavorite').removeClass("fa fa-star");
					$('#unfavorite').addClass("fa fa-star-o");
				});
				$("#unfav").mouseleave(function(){
					$('#unfavorite').removeClass("fa fa-star-o");
					$('#unfavorite').addClass("fa fa-star");
				});

				$("#delete").mouseenter(function(){
					$("#delete").css('color', '#666666');
				});
				$("#delete").mouseleave(function(){
					$("#delete").css('color', '#a6a6a6');
				});
			});
		</script>
	</head>
	<body>
		<div id = "wrapper">
			<!-- Banner and login button -->
			<?php include("draft.php"); ?>
			<!-- Page Content -->
			<div class = "content">
				<?php 
					
					$q = "SELECT recipeName, recipeImage,recipeDescription, recipeIngredient, recipeInstruction, difficulty, voteUp FROM recipedata WHERE recipeID = ".$ID;
					$detail = $connect->query($q);
					$cont = $detail->fetch_assoc();
					echo "<table>";
					echo "<tr><td colspan='2'><h1><b>".$cont['recipeName']."</b></h1></td></tr>";
					echo "<tr><td colspan='2'><p>".$cont['recipeDescription']."</p></td></tr>";
					echo "<tr><td colspan='2'><h3><b>Difficulty: ".$cont['difficulty']."</b></h3></td></tr>";
					echo "<tr><td colspan='2'><img src=".$cont['recipeImage']." alt=".$cont['recipeName']." style='width: 80%; margin: 20px 20px 20px 20px;'></td></tr>";
					echo "<tr><td colspan='2'><h3><b>How to cook this</b></h3></td></tr>";
					echo "<tr><td colspan='2'><div style='width: 300px; float: left;'><h3>Ingredients</h3><p>".$cont['recipeIngredient']."</p></div>";
					echo "<div style='float: left; width: 70%;'><h3>Instructions</h3><p>".$cont['recipeInstruction']."</p></div></td></tr>";
					echo "<tr><td>";
					if (isset($_SESSION['loggedin'])){
						$like = "SELECT voteID FROM voting WHERE userID = ".$_SESSION['userID']." AND recipeID = ".$ID;
						$checkLike = $connect->query($like);
						$row = $checkLike->fetch_assoc();

						if($row['voteID'] == NULL){
							echo "<a href='Like.php?id=".$ID."' title='Like'><h2 style='text-decoration: none;'><i class='fa fa-thumbs-o-up' id='like' style='color: #0066cc'></i>   ".$cont['voteUp']." Likes</h2></a>";
						}
						else{
							echo "<a href='Unlike.php?id=".$row['voteID']."' title='Unlike'><h2 style='text-decoration: none;'><i class='fa fa-thumbs-up' id='unlike' style='color: #0066cc'></i>   ".$cont['voteUp']." Likes</h2></a>";
						}
					}
					else{
						echo "<h2><i class='fa fa-thumbs-o-up' style='color: #0066cc'></i>   ".$cont['voteUp']."</h2>";
					}
					
					echo "</td>";
					echo "<td>";
					if(isset($_SESSION['loggedin'])){
						$fav = "SELECT favoriteID FROM favorite WHERE userID = ".$_SESSION['userID']." AND recipeID = ".$ID;
						$checkFav = $connect->query($fav);
						$row = $checkFav->fetch_assoc();
						if($row['favoriteID'] == NULL){
							echo "<a href='Favorite.php?id=".$ID."' title='Mark as favorite'><h2 id='fav'  style='text-decoration: none;'><i class='fa fa-star-o' id='favorite' style='color: #ffb300'></i>Favorite<h2></a>";
						}
						else{
							echo "<a href='Unfavorite.php?id=".$row['favoriteID']."' title='Unmark as favorite'><h2 id='unfav' style='text-decoration: none;'><i class='fa fa-star' id='unfavorite' style='color: #ffb300'></i>Favorite<h2></a>";
						}
					}
					echo "</td></tr>";
					echo "<tr><td><h2><i class='fa fa-commenting-o'></i>   Comments</h2></td></tr>";
					echo "</table>";
					if(isset($_SESSION['loggedin'])){
						echo "<form action='Comment.php?id=".$ID."' method='post'>";
						echo "<table>";
						echo "<tr><td><textarea placeholder='Enter your comment or suggestion here' name='commentContent' required></textarea></td></tr>";
						echo "<tr><td><button type='submit' id='comment-submit'>Submit</button></td></tr>";
						echo "</table>";
						echo "</form>";
					}
					$q2 = "SELECT comment.commentID, comment.userID, comment.comment, comment.commentDate, users.userName FROM comment, users WHERE comment.userID = users.userId AND recipeID = '".$ID."' ORDER BY commentDate asc;";
					$comment = mysqli_query($connect, $q2);
					if ($comment->num_rows > 0){
						while($row = mysqli_fetch_assoc($comment)) {
							echo "<div class='comment-container'>";
							echo "<table>";
							echo "<tr><td style='width: 200px;'><p>".$row['userName']."</p></td>";
							echo "<td rowspan='2' style='vertical-align: top;'><p >".$row['comment']."</p></td>";
							echo "<td rowspan='2' style='vertical-align: top; width: 25px;'>";
							if(isset($_SESSION['loggedin'])){
								if($row['userID'] == $_SESSION['userID']){
									//echo "<i class='fa fa-pencil' style='color: #a6a6a6;'></i>  ";
									echo "<a href='DeleteComment.php?id=".$row['commentID']."' title='Delete comment'><i class='fa fa-trash' id='delete' style='color: #a6a6a6;'></i></a>";
								}
							}
							
							echo "</td></tr>";
							echo "<tr><td><p>".$row['commentDate']."</p></td></tr>";
							echo "</table>";
							echo "</div>";
						}
					}
				?>
				
			</div>
			
	</body>

	<script>
	// Get the modal
	var modal = document.getElementById('id01');

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
	    if (event.target == modal) {
	        modal.style.display = "none";
	    }
	}
	</script>
</html>
