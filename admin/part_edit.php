<?php
$pageTitle='Add / Edit Part';
require_once __DIR__.'/includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$part = ['part_code'=>'','name'=>'','category_id'=>'','brand_id'=>'','garage_id'=>'','price'=>'','stock'=>0,'description'=>'','image_path'=>'','is_active'=>1];
if($id){
    $s=$pdo->prepare("SELECT * FROM spare_parts WHERE part_id=?"); $s->execute([$id]);
    $part = $s->fetch() ?: $part;
}
$cats   = $pdo->query("SELECT * FROM part_categories ORDER BY name")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$garages= $pdo->query("SELECT * FROM garages ORDER BY name")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $fields=['part_code','name','category_id','brand_id','garage_id','price','stock','description','is_active'];
    $d=[]; foreach($fields as $f) $d[$f]=trim($_POST[$f]??'');
    $d['is_active']= isset($_POST['is_active'])?1:0;

    $imgName = $part['image_path'];
    if(!empty($_FILES['image']['name'])){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','webp','gif'])){
            $imgName = uniqid('part_').'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR.$imgName);
            if($part['image_path'] && file_exists(UPLOAD_DIR.$part['image_path'])) @unlink(UPLOAD_DIR.$part['image_path']);
        }
    }
    if($id){
        $pdo->prepare("UPDATE spare_parts SET part_code=?,name=?,category_id=?,brand_id=?,garage_id=?,price=?,stock=?,description=?,image_path=?,is_active=? WHERE part_id=?")
            ->execute([$d['part_code'],$d['name'],$d['category_id']?:null,$d['brand_id']?:null,$d['garage_id']?:null,
                       $d['price'],$d['stock'],$d['description'],$imgName,$d['is_active'],$id]);
    } else {
        $pdo->prepare("INSERT INTO spare_parts (part_code,name,category_id,brand_id,garage_id,price,stock,description,image_path,is_active) VALUES (?,?,?,?,?,?,?,?,?,?)")
            ->execute([$d['part_code'],$d['name'],$d['category_id']?:null,$d['brand_id']?:null,$d['garage_id']?:null,
                       $d['price'],$d['stock'],$d['description'],$imgName,$d['is_active']]);
    }
    header('Location: parts.php?msg=saved'); exit;
}
?>
<div class="d-flex justify-content-between mb-3">
  <h4 class="fw-bold mb-0"><?= $id?'Edit':'Add New' ?> Spare Part</h4>
  <a href="parts.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card p-4">
  <form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-4"><label class="form-label">Part Code *</label>
      <input name="part_code" class="form-control" required value="<?= htmlspecialchars($part['part_code']) ?>"></div>
    <div class="col-md-8"><label class="form-label">Name *</label>
      <input name="name" class="form-control" required value="<?= htmlspecialchars($part['name']) ?>"></div>

    <div class="col-md-4"><label class="form-label">Category</label>
      <select name="category_id" class="form-select">
        <option value="">--</option>
        <?php foreach($cats as $c): ?><option value="<?= $c['category_id'] ?>" <?= $part['category_id']==$c['category_id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
      </select></div>
    <div class="col-md-4"><label class="form-label">Brand</label>
      <select name="brand_id" class="form-select">
        <option value="">--</option>
        <?php foreach($brands as $b): ?><option value="<?= $b['brand_id'] ?>" <?= $part['brand_id']==$b['brand_id']?'selected':'' ?>><?= htmlspecialchars($b['name']) ?></option><?php endforeach; ?>
      </select></div>
    <div class="col-md-4"><label class="form-label">Garage</label>
      <select name="garage_id" class="form-select">
        <option value="">--</option>
        <?php foreach($garages as $g): ?><option value="<?= $g['garage_id'] ?>" <?= $part['garage_id']==$g['garage_id']?'selected':'' ?>><?= htmlspecialchars($g['name']) ?></option><?php endforeach; ?>
      </select></div>

    <div class="col-md-4"><label class="form-label">Price (KES) *</label>
      <input type="number" step="0.01" name="price" class="form-control" required value="<?= htmlspecialchars($part['price']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Stock</label>
      <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($part['stock']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Image</label>
      <input type="file" name="image" class="form-control" accept="image/*"></div>

    <?php if($part['image_path']): ?>
    <div class="col-12"><img src="<?= UPLOAD_URL.htmlspecialchars($part['image_path']) ?>" style="max-height:120px;border-radius:6px;"></div>
    <?php endif; ?>

    <div class="col-12"><label class="form-label">Description</label>
      <textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($part['description']) ?></textarea></div>

    <div class="col-12 form-check ms-2">
      <input type="checkbox" name="is_active" class="form-check-input" id="ia" <?= $part['is_active']?'checked':'' ?>>
      <label class="form-check-label" for="ia">Active (visible on site)</label>
    </div>
    <div class="col-12 text-end"><button class="btn btn-warning fw-bold"><i class="bi bi-save"></i> Save Part</button></div>
  </form>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>