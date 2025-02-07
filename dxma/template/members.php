<?php if (!defined('DXMA_VERSION')) die(); ?>
<h2>Search</h2>
<form action="." method="get" id="searchform">
    <table>
        <tr>
            <th>By name</th>
            <td><input name="q" id="search-q" type="text" value="<?= htmlspecialchars($_GET["q"] ?? "") ?>" maxlength="256" /></td>
        </tr>
        <tr>
            <th>Order</th>
            <td>
                <select name="order" id="order" form="searchform" required value="<?= htmlspecialchars($_GET['order'] ?? '') ?>">
                    <option value="name_a" <?= "name_a" === ($_GET['order'] ?? '') || empty($_GET['order']) ? "selected" : "" ?>>Name (ascending)</option>
                    <option value="name_d" <?= "name_d" === ($_GET['order'] ?? '') ? "selected" : "" ?>>Name (descending)</option>
                    <option value="jdate_a" <?= "jdate_a" === ($_GET['order'] ?? '') ? "selected" : "" ?>>Join date (oldest first)</option>
                    <option value="jdate_d" <?= "jdate_d" === ($_GET['order'] ?? '') ? "selected" : "" ?>>Join date (newest first)</option>
                </select>
            </td>
        </tr>
    </table>
    <input type="submit" value="Search" />
</form>
<form action="." method="get" id="searchformreset">
    <input type="submit" value="Reset" />
</form>
<h2>User List</h2>
<?= fragment("memberlist", $members) ?>
<br />
<?= fragment("pages", [ "num" => $pageNum, "count" => $pageCount ]) ?>
