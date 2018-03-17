<form action="/tool/httpapi/new" method="POST">
    <?= csrf_feild() ?>
    <input type="hidden" name="project" value="<?= $project->id ?>">

    <label>
        <span class="label-title-sm">name</span>
        <input name="name" type="text" required>
    </label>

    <label>
        <span class="label-title-sm">path</span>
        <input name="path" type="text" required>
    </label>
    
     <label>
        <span class="label-title-sm">method</span>
        <select name="method">
            <option value="get">GET</option>
            <option value="get">POST</option>
            <option value="get">PUT</option>
            <option value="get">PATCH</option>
            <option value="get">DELETE</option>
        </select>
        <button type="button">request</button>
        <button type="button">save</button>
    </label>
   
</form>

