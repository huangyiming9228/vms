function logout() {
  localStorage.removeItem('emp_no');
  window.location.href = '../../index.html';
}