<?php
$pageTitle='Vehicle Brands';
require_once __DIR__.'/includes/admin_header.php';

if($_GET['delete'] ?? false){
    $id=(int)$_GET['delete'];
    $pdo->prepare("DELETE FROM brands WHERE brand_id=?")->execute([$id]);
    header('Location: brands.php?msg=deleted'); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['brand_id'] ?? 0);
    $name=trim($_POST['name'] ?? '');
    $imgName=null;
    if(!empty($_FILES['logo']['name'])){
        $ext=strtolower(pathinfo($_FILES['logo']['name'],PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','webp','svg','gif'])){
            $imgName='brand_'.uniqid().'.'.$ext;
            move_uploaded_file($_FILES['logo']['tmp_name'],UPLOAD_DIR.$imgName);
        }
    }
    if($id){
        if($imgName) $pdo->prepare("UPDATE brands SET name=?, logo_path=? WHERE brand_id=?")->execute([$name,$imgName,$id]);
        else         $pdo->prepare("UPDATE brands SET name=? WHERE brand_id=?")->execute([$name,$id]);
    } else {
        $pdo->prepare("INSERT INTO brands (name,logo_path) VALUES (?,?)")->execute([$name,$imgName]);
    }
    header('Location: brands.php?msg=saved'); exit;
}
$brands=$pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$edit = null;
if($_GET['edit'] ?? false){ $s=$pdo->prepare("SELECT * FROM brands WHERE brand_id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
?>
<?php if($_GET['msg'] ?? false): ?><div class="alert alert-success alert-auto"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card p-3">
      <h5 class="fw-bold"><?= $edit?'Edit':'Add' ?> Brand</h5>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="brand_id" value="<?= $edit['brand_id'] ?? '' ?>">
        <div class="mb-2"><label class="form-label">Name *</label>
          <input name="name" class="form-control" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>"></div>
        <div class="mb-2"><label class="form-label">Logo</label>
          <input type="file" name="logo" class="form-control" accept="image/*"></div>
        <?php if(!empty($edit['logo_path'])): ?><img src="<?= UPLOAD_URL.htmlspecialchars($edit['logo_path']) ?>" style="height:60px;" class="mb-2"><?php endif; ?>
        <button class="btn btn-warning w-100 fw-bold">Save</button>
        <?php if($edit): ?><a href="brands.php" class="btn btn-link w-100">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card p-3">
      <h5 class="fw-bold">All Brands (<?= count($brands) ?>)</h5>
      <table class="table table-hover align-middle">
        <thead><tr><th>Logo</th><th>Name</th><th></th></tr></thead>
        <tbody>
        <?php foreach($brands as $b): ?>
          <tr>
            <td><?php if($b['logo_path']): ?><img src="<?= UPLOAD_URL.htmlspecialchars($b['logo_path']) ?>" style="height:32px;"><?php else: ?><i class="bi bi-car-front"></i><?php endif; ?></td>
            <td><?= htmlspecialchars($b['name']) ?></td>
            <td class="text-end">
              <a href="?edit=<?= $b['brand_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="?delete=<?= $b['brand_id'] ?>" data-confirm="Delete this brand? This cannot be undone." class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>