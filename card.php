<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Photo Card</title>
<style>
    /* styles.css */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f4f4f4;
}

.photo-card {
  width: 300px;
  border: 1px solid #ddd;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  background-color: white;
  overflow: hidden;
  text-align: center;
}

.photo {
  width: 100%;
  height: auto;
  border-bottom: 1px solid #ddd;
}

.details {
  padding: 15px;
}

.details h2 {
  margin: 0 0 10px;
  font-size: 1.5em;
}

.details p {
  font-size: 1em;
  color: #555;
  margin: 0 0 15px;
}

button {
  padding: 10px 20px;
  font-size: 1em;
  color: white;
  background-color: #007BFF;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}

</style>
</head>
<body>
  <div class="photo-card">
    <img src="photo.jpg" alt="Beautiful Photo" class="photo">
    <div class="details">
      <h2>Card Title</h2>
      <p>Description of the photo or card content. Add more text here if needed.</p>
      <button id="button">Click Me</button>
    </div>
  </div>

  <script>
document.getElementById('button').addEventListener('click', function() {
  alert('Button Clicked!');
});

  </script>
</body>
</html>
