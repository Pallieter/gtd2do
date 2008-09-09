<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  
  <script>
  $(document).ready(function(){
    
    function showValues() {
      var str = $("form").serialize();
      $("#results").text(str);
      $("#text2").text(str);
    }

    $(":checkbox, :radio").click(showValues);
    $("select").change(showValues);
    showValues();

  });
  </script>
  <style>
  body, select { font-size:12px; }
  form { margin:5px; }
  p { color:red; margin:5px; font-size:14px; }
  b { color:blue; }
  </style>
</head>
<body>
  <form>
    <select name="single">
      <option>Single</option>
      <option>Single2</option>
    </select>
    <select name="multiple" multiple="multiple">
      <option selected="selected">Multiple</option>
      <option>Multiple2</option>
      <option selected="selected">Multiple3</option>
    </select><br/>
    <input type="checkbox" name="check" value="check1" id="ch1"/>
    <label for="ch1">check1</label>
    <input type="checkbox" name="check" value="check2" checked="checked" id="ch2"/>
    <label for="ch2">check2</label>
    <input type="radio" name="radio" value="radio1" checked="checked" id="r1"/>
    <label for="r1">radio1</label>
    <input type="radio" name="radio" value="radio2" id="r2"/>
    <label for="r2">radio2</label>
    <textarea name="text1">
    </textarea>
    <textarea name="text2">
    </textarea>
  </form>
  <p><tt id="results"></tt></p>
</body>
</html>

