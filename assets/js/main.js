// Auto-dismiss alerts
setTimeout(()=>document.querySelectorAll('.alert-auto').forEach(a=>a.remove()), 4000);

// Fetch client location for booking
window.getLocation = function(){
  return new Promise((resolve)=>{
    if(!navigator.geolocation) return resolve(null);
    navigator.geolocation.getCurrentPosition(
      p => resolve({lat:p.coords.latitude, lng:p.coords.longitude}),
      () => resolve(null)
    );
  });
};
