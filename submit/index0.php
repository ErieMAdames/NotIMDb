<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles.css" media="all" />
  <title>Not IMDB</title>
</head>
<?php
  $messages = array();
  $db = new PDO('sqlite:../data.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  function exec_query($db, $sql, $params) {
    $query = $db->prepare($sql);
    if ($query and $query->execute($params)) {
      return $query;
    }
    return NULL;
  };
  CONST SEARCH_FIELDS = ["title" => "By Title",
                         "year" =>"By Year",
                         "rating" => "By Rating",
                         "director" => "By Director"];
  CONST GENRES = ["Action", "Animation", "Adventure", "Biography", "Romance",
                  "Crime", "Comedy", "Drama", "Family", "Fantasy", "History", "Horror",
                  "Musical", "Mystery", "Sci-fi", "Thriller", "War", "Western"];
  if (isset($_GET['search']) and isset($_GET['category'])) {
    $dosearch = TRUE;
    $category = filter_input(INPUT_GET, "category", FILTER_SANITIZE_STRING);
    if (in_array($category, array_keys(SEARCH_FIELDS))){
      $search_field = $category;
    }else{
      $search_field = NULL;
      array_push($messages, "Invalid category for search.");
      $dosearch = FALSE;
    }
    $search = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
    trim($search);
    }
  else {
    $dosearch = FALSE;
    $category = NULL;
    $search = NULL;
    }
  function display($record) {
    ?>
    <tr>
      <td><?php echo htmlspecialchars($record["title"]);?></td>
      <td><?php echo htmlspecialchars($record["year"]);?></td>
      <td><?php echo htmlspecialchars($record["rating"]);?>/10</td>
      <td><?php echo htmlspecialchars($record["director"]);?></td>
    </tr>
  <?php
}
?>
<body>
  <h1 id = "title">Not IMDb</h1>
  <div class = "main">
    <p class = "description">In Not IMDb we want to provide you with a simple way to find the perfect movie for you.
       We let you search for movies by genre, so that you can find your new favorite action flics,
       thrillers, or comedies. If you want to contribute to our ever-expanding database, click
       <a href="submit">here</a>.</p>
    <?php
    if (strlen($_GET['genre']) > 1){
      $gt = filter_input(INPUT_GET, "genre", FILTER_SANITIZE_STRING);
      echo "<h2 class = 'genre'>" . $gt . "</h2>";
    }
    else {
      echo "<h2 class = 'genre'>All genres</h2>";
    }
    ?>
    <form id="searchForm" action="index.php" method="get">
      Search:
      <select name="genre">
        <option value="" selected disabled>Select genre</option>
        <?php
        foreach(GENRES as $genre){
          ?>
          <option value="<?php echo $genre;?>"><?php echo $genre;?></option>
          <?php
        } ?>
      </select>
      <select name="category">
        <option value="" selected disabled>Search By</option>
        <?php
        foreach(SEARCH_FIELDS as $field_name => $label){
          ?>
          <option value="<?php echo $field_name;?>"><?php echo $label;?></option>
          <?php
        }
        ?>
      </select>
      <input type="text" name="search"/>
      <button type="submit">Search</button>
    </form>
    <?php
    if ($dosearch) {
      if (isset($_GET['genre'])){
        $genre_search = filter_input(INPUT_GET, "genre", FILTER_SANITIZE_STRING);
        if (in_array($genre_search, GENRES)){
          if ($search_field == 'year'){
            $sql = "SELECT * FROM movies WHERE " . $search_field . " LIKE :search AND genre LIKE '%' || :genre_search || '%'";
            }
          else {
            $sql = "SELECT * FROM movies WHERE " . $search_field . " LIKE '%' || :search || '%' AND genre LIKE '%' || :genre_search || '%'";
            }
          $params = array(':search' => $search,
                          ':genre_search' => $genre_search);
          }
        else {
          array_push($messages, "Invalid genre for search.");
          }
        }
      else {
        if ($search_field == 'year'){
          $sql = "SELECT * FROM movies WHERE " . $search_field . " LIKE :search";
          }
        else {
          $sql = "SELECT * FROM movies WHERE " . $search_field . " LIKE '%' || :search || '%'";
          }
        $params = array(':search' => $search);
          }
      }
      else {
        if (isset($_GET['genre'])){
        $genre_search = filter_input(INPUT_GET, "genre", FILTER_SANITIZE_STRING);
          if (in_array($genre_search, GENRES)){
            $sql = "SELECT * FROM movies WHERE genre LIKE '%' || :genre_search || '%'";
            $params = array (':genre_search' => $genre_search);
            }
          else {
            array_push($messages, "Invalid genre");
            }
          }
        else {
          $sql = "SELECT * FROM movies";
          $params = array();
          }
        }
    $records = exec_query($db, $sql, $params);
    ?>
    <table id = "movies">
      <tr>
        <th>Title</th>
        <th>Year</th>
        <th>Rating</th>
        <th>Director</th>
      </tr>
      <?php
        foreach($messages as $mes){
          echo "<h1>" . $mes . "</h1>";
        }
        foreach($records as $rec){
          display($rec);
        }
      ?>
    </table>
  </div>
</body>
</html>
