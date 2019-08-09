<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="../styles.css" media="all" />

  <title>Not IMDb</title>
</head>
<?php
  $db = new PDO('sqlite:../data.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  function exec_query($db, $sql, $params) {
    $query = $db->prepare($sql);
    if ($query and $query->execute($params)) {
      return $query;
    }
    return NULL;
  };
  CONST GENRES = ["Action", "Animation", "Adventure", "Biography", "Romance",
                  "Crime", "Comedy", "Drama", "Family", "Fantasy", "History", "Horror",
                  "Musical", "Mystery", "Sci-fi", "Thriller", "War", "Western"];
  if (isset($_POST["submit"])) {
  $genre1 = filter_input(INPUT_POST, 'genre1', FILTER_SANITIZE_STRING);
  $genre2 = filter_input(INPUT_POST, 'genre2', FILTER_SANITIZE_STRING);
  $genre3 = filter_input(INPUT_POST, 'genre3', FILTER_SANITIZE_STRING);
  $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
  $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT);
  $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $director = filter_input(INPUT_POST, 'director', FILTER_SANITIZE_STRING);
  if (isset($genre2)) {
    if (isset($genre3)){
        $genre = $genre1 . ", " . $genre2 . ", " . $genre3;
      }
    else{
      $genre = $genre1 . ", " . $genre2;
    }
  }
  else {
    $genre = $genre1;
  }

  $sql = "INSERT INTO movies (title, year, rating, director, genre)
          VALUES (:title, :year, :rating, :director, :genre)";
  $params = array(':title' => $title,
                  ':year' => $year,
                  ':rating' => $rating,
                  ':director' => $director,
                  ':genre' => $genre);
  $result = exec_query($db, $sql, $params);
}
?>
<body>
  <h1 id = "title">Not IMDb</h1>
  <div class = "main">
    <h2 class = "genre">Add Entry</h2>
    <p>Please add at least one genre and fill out every other field. <a href="..">Go back</a>.</p>
    <?php
    if ($result) {
      echo "<p class ='recorded'>Your review has been recorded. Thank you!</p>";
    } elseif (!$result and strlen($genre1)>0){
      echo "<p>Failed to add review.</p>";
    }
    ?>
    <form id="submit" action="index.php" method="post">
      <ul>
        <li class = "subfields">
          Select Genres:
          <select name="genre1" required>
            <option value="" selected disabled>Select genre</option>
            <?php
            foreach(GENRES as $genre){
              ?>
              <option value="<?php echo $genre;?>"><?php echo $genre;?></option>
              <?php
            } ?>
          </select>
        </li>
        <li class = "subfields">
          <select name="genre2">
            <option value="" selected disabled>Select genre</option>
            <?php
            foreach(GENRES as $genre){
              ?>
              <option value="<?php echo $genre;?>"><?php echo $genre;?></option>
              <?php
            } ?>
          </select required>
        </li>
        <li class = "subfields">
          <select name="genre3">
            <option value="" selected disabled>Select genre</option>
            <?php
            foreach(GENRES as $genre){
              ?>
              <option value="<?php echo $genre;?>"><?php echo $genre;?></option>
              <?php
            } ?>
          </select>
        </li>
        <li class = "subfields">
          Title:
          <input type="text" name="title" placeholder="Title" required/>
        </li>
        <li class = "subfields">
          Year:
          <input type="number" name="year" min="1850" max="2018" placeholder="Year"required/>
        </li>
        <li class = "subfields">
          Rating:
          <input type="number" step=".1" name="rating" min="0" max="10" placeholder="Out of 10" required>
        </li>
        <li class = "subfields">
          Director:
          <input type="text" name="director" placeholder="Director" required/>
        </li>
        <li class = "subfields">
        <button name="submit" type="submit" class = "submitbutton">Submit</button>
        </li>
      </ul>

    </form>
  </div>
</body>
</html>
