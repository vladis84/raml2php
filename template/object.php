<?php
/* @var $type Source\Type\ObjectType */
require_once 'helper.php';
?>
<?= '<?php' ?>


/**
 * <?= $type->description ?>

*/
class <?= $type->name ?>

{
<?php
foreach ($type->properties as $property) {
    $templateName = in_array($property->type, ['object']) ? $property->type : 'type';
    
    includeTemplate($templateName, $property);
}
?>

}