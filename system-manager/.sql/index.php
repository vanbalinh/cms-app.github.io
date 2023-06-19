<html>
    <head>
        <style type="text/css">
            *{
                font-family: arial;
                font-size: 13px;
            }

            .value,.line{
                height: 25px;
                line-height: 25px;
                padding: 0 10px;
                border-bottom: 1px dotted #ccc;
            }

            .container{
                display: flex;
            }

            .container>.wrapper-line{
                border-right: 1px dotted #ccc;
                border-left: 1px dotted #ccc;
                text-align: center;
            }
            .container>.wrapper-value{
                flex: 1;
                border-right: 1px dotted #ccc;
            }
            .green-color{
                color: green;
            }
            .red-color{
                color: red;
            }
        </style>
    </head>
    <body>
<?php
include_once __DIR__ . './get_content.php';
$content = getContent();
$l = 1;
$line = "";
$value = "";
foreach ($content as $f) {
    $value .= "<div class='value green-color'>\n-- START " . $f->name . ".sql\n</div>";
    $line .= "<div class='line'>" . $l++ . "</div>";
    foreach ($f->data as $c) {
        $value .= "<div class='value'>" . $c . "</div>";
        $line .= "<div class='line'>" . $l++ . "</div>";
    }
    $value .= "<div class='value green-color'>\n-- END " . $f->name . ".sql\n</div>";
    $line .= "<div class='line'>" . $l++ . "</div>";
    $value .= "<div class='value red-color'>\n-- =====================================*****=====================================\n</div>";
    $line .= "<div class='line'>" . $l++ . "</div>";
    $l = $l + 4;
}
echo "<div class='container'>
        <div class='wrapper-line'>
            " . $line . "
        </div>
        <div class='wrapper-value'>
            " . $value . "
        </div>
    </div>";
?>
<div>
    <button onclick="copy()">Sao chép nội dung</button>
</div>
</body>
<script type="text/javascript">
    function copy(){
        let values = "";
        const valueElement = document.querySelectorAll(".wrapper-value > .value");
        valueElement.forEach(e=>{
            values+= e.textContent;
        })
        navigator.clipboard.writeText(values);
    }
</script>

</html>