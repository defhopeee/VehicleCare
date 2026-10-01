<?php
$pageTitle='Add / Edit Mechanic';
require_once __DIR__.'/includes/admin_header.php';

$id=(int)($_GET['id'] ?? 0);
$m=['full_name'=>'','garage_id'=>'','specialty_fault_id'=>'','phone'=>'','email'=>'','photo_path'=>'','bio'=>'','rating'=>5.0,'is_available'=>1];
if($id){ $s=$pdo->prepare("SELECT * FROM mechanics WHERE mechanic_id=?"); $s->execute([$id]); $m=$s->fetch()?:$m; }

$garages=$pdo->query("SELECT * FROM garages ORDER BY name")->fetchAll();
$faults =$pdo->query("SELECT * FROM faults ORDER BY name")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $d=[
        'full_name'=>trim($_POST['full_name']),
        'garage_id'=>$_POST['garage_id']?:null,
        'specialty_fault_id'=>$_POST['specialty_fault_id']?:null,
        'phone'=>trim($_POST['phone']),
        'email'=>trim($_POST['email']),
        'bio'=>trim($_POST['bio']),
        'rating'=>(float)($_POST['rating'] ?? 5),
        'is_available'=>isset($_POST['is_available'])?1:0,
    ];
    $photo=$m['photo_path'];
    if(!empty($_FILES['photo']['name'])){
        $ext=strtolower(pathinfo($_FILES['photo']['name'],PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','webp','gif'])){
            $photo='mech_'.uniqid().'.'.$ext;
            move_uploaded_file($_FILES['photo']['tmp_name'],UPLOAD_DIR.$photo);
            if($m['photo_path'] && file_exists(UPLOAD_DIR.$m['photo_path'])) @unlink(UPLOAD_DIR.$m['photo_path']);
        }
    }
    if($id){
        $pdo->prepare("UPDATE mechanics SET full_name=?,garage_id=?,specialty_fault_id=?,phone=?,email=?,bio=?,rating=?,is_available=?,photo_path=? WHERE mechanic_id=?")
            ->execute([$d['full_name'],$d['garage_id'],$d['specialty_fault_id'],$d['phone'],$d['email'],$d['bio'],$d['rating'],$d['is_available'],$photo,$id]);
    } else {
        $pdo->prepare("INSERT INTO mechanics (full_name,garage_id,specialty_fault_id,phone,email,bio,rating,is_available,photo_path)
                       VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$d['full_name'],$d['garage_id'],$d['specialty_fault_id'],$d['phone'],$d['email'],$d['bio'],$d['rating'],$d['is_available'],$photo]);
    }
    header('Location: mechanics.php?msg=saved'); exit;
}
?>
<div class="d-flex justify-content-between mb-3">
  <h4 class="fw-bold mb-0"><?= $id?'Edit':'Add' ?> Mechanic</h4>
  <a href="mechanics.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card p-4">
  <form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6"><label class="form-label">Full Name *</label><input name="full_name" class="form-control" required value="<?= htmlspecialchars($m['full_name']) ?>"></div>
    <div class="col-md-6"><label class="form-label">Garage</label>
      <select name="garage_id" class="form-select">
        <option value="">-- None --</option>
        <?php foreach($garages as $g): ?><option value="<?= $g['garage_id'] ?>" <?= $m['garage_id']==$g['garage_id']?'selected':'' ?>><?= htmlspecialchars($g['name']) ?></option><?php endforeach; ?>
      </select></div>
    <div class="col-md-6"><label class="form-label">Specialty Fault *</label>
      <select name="specialty_fault_id" class="form-select" required>
        <option value="">-- Select --</option>
        <?php foreach($faults as $f): ?><option value="<?= $f['fault_id'] ?>" <?= $m['specialty_fault_id']==$f['fault_id']?'selected':'' ?>><?= htmlspecialchars($f['name']) ?></option><?php endforeach; ?>
      </select></div>
    <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($m['phone']) ?>"></div>
    <div class="col-md-6"><label class="form-label">Email</label><input name="email" class="form-control" value="<?= htmlspecialchars($m['email']) ?>"></div>
    <div class="col-md-6">
      <label class="form-label">Starting Rating (1 to 5)</label>
      <input type="number" step="0.1" min="1" max="5" name="rating" class="form-control" value="<?= htmlspecialchars($m['rating']) ?>">
      <div class="form-text">Shown to customers until real reviews exist, there is no customer review system yet.</div>
    </div>
    <div class="col-md-6"><label class="form-label">Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
    <?php if($m['photo_path']): ?><div class="col-md-6"><img src="<?= UPLOAD_URL.htmlspecialchars($m['photo_path']) ?>" style="max-height:90px;border-radius:50%;"></div><?php endif; ?>
    <div class="col-12"><label class="form-label">Bio</label><textarea name="bio" rows="3" class="form-control"><?= htmlspecialchars($m['bio']) ?></textarea></div>
    <div class="col-12 form-check ms-2"><input type="checkbox" name="is_available" class="form-check-input" id="ia" <?= $m['is_available']?'checked':'' ?>><label class="form-check-label" for="ia">Available for bookings</label></div>
    <div class="col-12 text-end"><button class="btn btn-warning fw-bold"><i class="bi bi-save"></i> Save</button></div>
  </form>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>