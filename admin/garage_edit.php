<?php
$pageTitle='Add / Edit Garage';
require_once __DIR__.'/includes/admin_header.php';

$id=(int)($_GET['id'] ?? 0);
$g=['name'=>'','county'=>'','physical_location'=>'','latitude'=>'','longitude'=>'','phone'=>'','whatsapp'=>'','email'=>'','opening_hours'=>'24/7','description'=>'','image_path'=>'','is_active'=>1];
if($id){ $s=$pdo->prepare("SELECT * FROM garages WHERE garage_id=?"); $s->execute([$id]); $g=$s->fetch()?:$g; }

if($_SERVER['REQUEST_METHOD']==='POST'){
    $fields=['name','county','physical_location','latitude','longitude','phone','whatsapp','email','opening_hours','description'];
    $d=[]; foreach($fields as $f) $d[$f]=trim($_POST[$f] ?? '');
    $active=isset($_POST['is_active'])?1:0;

    $img=$g['image_path'];
    if(!empty($_FILES['image']['name'])){
        $ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','webp','gif'])){
            $img='garage_'.uniqid().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'],UPLOAD_DIR.$img);
            if($g['image_path'] && file_exists(UPLOAD_DIR.$g['image_path'])) @unlink(UPLOAD_DIR.$g['image_path']);
        }
    }
    if($id){
        $pdo->prepare("UPDATE garages SET name=?,county=?,physical_location=?,latitude=?,longitude=?,phone=?,whatsapp=?,email=?,opening_hours=?,description=?,image_path=?,is_active=? WHERE garage_id=?")
            ->execute([$d['name'],$d['county'],$d['physical_location'],$d['latitude']?:null,$d['longitude']?:null,$d['phone'],$d['whatsapp'],$d['email'],$d['opening_hours'],$d['description'],$img,$active,$id]);
    } else {
        $pdo->prepare("INSERT INTO garages (name,county,physical_location,latitude,longitude,phone,whatsapp,email,opening_hours,description,image_path,is_active)
                       VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$d['name'],$d['county'],$d['physical_location'],$d['latitude']?:null,$d['longitude']?:null,$d['phone'],$d['whatsapp'],$d['email'],$d['opening_hours'],$d['description'],$img,$active]);
    }
    header('Location: garages.php?msg=saved'); exit;
}
?>
<div class="d-flex justify-content-between mb-3">
  <h4 class="fw-bold mb-0"><?= $id?'Edit':'Add' ?> Garage</h4>
  <a href="garages.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card p-4">
  <form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6"><label class="form-label">Name *</label><input name="name" class="form-control" required value="<?= htmlspecialchars($g['name']) ?>"></div>
    <div class="col-md-6"><label class="form-label">County</label><input name="county" class="form-control" value="<?= htmlspecialchars($g['county']) ?>"></div>
    <div class="col-12"><label class="form-label">Physical Location</label><input name="physical_location" class="form-control" value="<?= htmlspecialchars($g['physical_location']) ?>"></div>
    <div class="col-md-6"><label class="form-label">Latitude</label><input name="latitude" class="form-control" value="<?= htmlspecialchars($g['latitude']) ?>"></div>
    <div class="col-md-6"><label class="form-label">Longitude</label><input name="longitude" class="form-control" value="<?= htmlspecialchars($g['longitude']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($g['phone']) ?>"></div>
    <div class="col-md-4"><label class="form-label">WhatsApp</label><input name="whatsapp" class="form-control" value="<?= htmlspecialchars($g['whatsapp']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Email</label><input name="email" class="form-control" value="<?= htmlspecialchars($g['email']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Opening Hours</label><input name="opening_hours" class="form-control" value="<?= htmlspecialchars($g['opening_hours']) ?>"></div>
    <div class="col-md-8"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    <?php if($g['image_path']): ?><div class="col-12"><img src="<?= UPLOAD_URL.htmlspecialchars($g['image_path']) ?>" style="max-height:120px;border-radius:6px;"></div><?php endif; ?>
    <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($g['description']) ?></textarea></div>
    <div class="col-12 form-check ms-2"><input type="checkbox" name="is_active" class="form-check-input" id="ia" <?= $g['is_active']?'checked':'' ?>><label class="form-check-label" for="ia">Active</label></div>
    <div class="col-12 text-end"><button class="btn btn-warning fw-bold"><i class="bi bi-save"></i> Save</button></div>
  </form>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>