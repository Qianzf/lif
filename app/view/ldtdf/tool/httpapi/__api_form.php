<form action="/tool/httpapi/new" method="POST">
    <input type="hidden" name="project" value="<?= $project->id ?>">

    <label>
    <input name="name" type="text" required placeholder="<?= L('INPUT_TITLE') ?>" onKeyup="syncApiTitle(this)">

    <?php if (isset($cates) && iteratable($cates)) : ?>
    <select name="cate"> 
        <option value="0"><?= L('DEFAULT_CATE') ?></option>
    <?php foreach ($cates as $cate) : ?>
        <option value="<?= $cate->id ?>"><?= $cate->name ?></option>
    <?php endforeach ?>
    </select>
    <?php endif ?>

    <button type="button"><?= L('SAVE') ?></button>
    </label>

    <label>
    <input name="path" type="text" required placeholder="<?= L('INPUT_PATH') ?>">

    <select name="method">
        <option value="get">GET</option>yy
        <option value="post">POST</option>
        <option value="put">PUT</option>
        <option value="patch">PATCH</option>
        <option value="delete">DELETE</option>
    </select>
    
    <select name="apienv">
        <option><?= L('SELECT_ENV') ?></option>
        <?php if (isset($envs) && iteratable($envs)) : ?>
        <?php foreach ($envs as $env) : ?>
        <option value="<?= $env->id ?>"><?= $env->name ?></option>
        <?php endforeach ?>
        <?php endif ?>
    </select>

    <button type="button"><?= L('REQUEST') ?></button>
    </label>
</form>

