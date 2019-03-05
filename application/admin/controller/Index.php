<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
  // public function _initialize() {
  //   Session::set('admin_no', 'admin');
  //   Session::set('admin_name', '开发');
  // }

  // 渲染首页
  public function index(){
    if (!Session::has('admin_no')) {
      $this->redirect(BACK_LOGIN_URL);
    }
    return $this->fetch('index', [
      'menu_list' => $this->get_menu(),
      'name' => Session::get('admin_name'),
    ]);
  }

  // 渲染欢迎页
  public function welcome() {
    return $this->fetch('welcome');
  }

  // 渲染登陆页面
  public function login() {
    return $this->fetch('login');
  }

  // 登录验证
  public function login_check() {
    $admin_no = $_POST['admin_no'];
    $admin_psw = $_POST['admin_psw'];
    $admin_info = Db::table('admin_user')->where('admin_no', $admin_no)->find();
    if (!$admin_info) {
      return $this->error('账号不存在！');
    }
    if ($admin_psw == $admin_info['admin_psw']) {
      Session::set('admin_no', $admin_info['admin_no']);
      Session::set('admin_name', $admin_info['admin_name']);
      Session::set('station_id', $admin_info['station_id']);
      $this->success('登录成功！', url('index'));
    } else {
      $this->error('密码错误！');
    }
  }

  
  /**
   * 显示首页
   * @access protected
   * @return object    $view        首页模板
   */
  // public function index(){
  //   if(!Session::has('qd_id')){
  //     $this->redirect(QYBYB_WECHAT);
  //   }
  //   $view=new View();
  //   $menu_list=$this->get_menu();
  //   $view->assign('menu_list',$menu_list);
  //   $view->assign('name',Session::get('qd_name'));
  //   $view->assign('date', date('Y-m-d'));
  //   return $view->fetch('index_temp');
  // }

  /**
   * 显示密码设置
   * @access protected
   * @return object    $view        密码设置
   */
  public function password_setting(){
    $view=new View();
    $view->assign('title','密码修改');
    return $view->fetch('password_setting');
  }
  /**
   * 密码修改
   * @access protected
   * @return object    $view        密码修改
   */
  public function password_change($old_psw,$new_psw,$confirm_psw){
    if(empty($old_psw)||empty($new_psw)||empty($confirm_psw)){
        $this->error('密码不能为空');
    }
    $user = AdminUser::where('id',Session::get('qd_id'))->find();
    if(empty($user)){
        $this->error("账号不存在");
    }
    if($user['password'] !== md5($old_psw)){
        $this->error("原始密码不正确");
    }
    if($new_psw !== $confirm_psw){
        $this->error("两次输入的新密码不一致");
    }else{
        $user['password'] = md5($new_psw);
        $user->save();
        $this->success('操作成功');
    }
  }
  /**
   * 获取首页菜单列表
   * @access protected
   * @return object    $view        空白模板
   */
  public function get_menu(){
    $menu_list = [];
    $page_can_list = Db::table('menu_seek')
                     ->where('station_id', Session::get('station_id'))
                     ->column('page_id');
    $map_root_menu = array('leave'=>0,'parent'=>0,'id'=>array('IN',$page_can_list));
    $root_menu_list = Db::table('menu')->where($map_root_menu)
                                       ->order('sort_index asc')
                                       ->select();
    foreach ($root_menu_list as $root_key => $root_menu) {
      array_push($menu_list, ['data'=>$root_menu,'sub_menu'=>array()]);
      $map_sub_menu = array('leave'=>1,'parent'=>$root_menu['id'],'id'=>array('IN',$page_can_list));
      $sub_menu_list = Db::table('menu')->where($map_sub_menu)
                                        ->order('sort_index asc')
                                        ->select();
      foreach ($sub_menu_list as $sub_key => $sub_menu) {
        if(!$sub_menu['url']) {
            $sub_menu['url'] = 'javascript:;';
        } else {
            $sub_menu['url']=url($sub_menu['url']);
        }
        array_push($menu_list[$root_key]['sub_menu'],['data'=>$sub_menu, 'third_menu' => array()]);
      }
    }
    return $menu_list;
  }  
  /**
   * 登录
   * @access protected
   * @param array     $_POST             用户输入的账号密码   
   * @return object    $view             返回登录结果
   */
  public function log_in_chekc(){
    $account = $_POST['account'];
    $psw=$_POST['password'];
    if (empty($account)) {
      $this -> error('帐号必须！');
    } elseif (empty($psw)) {
      $this -> error('密码必须！');
    }
    $user = AdminUser::where('account',$account)->find();
    if(empty($user)){
      $this->error("账号不存在");
    }

    if($user['password']==md5($psw)){
      Session::set('qd_id', $user['id']);
      Session::set('qd_account', $user['account']);
      Session::set('qd_name', $user['name']);
      Session::set('qd_unit_id', $user['unit_id']);
      Session::set('qd_station_id', $user['station_id']);
      $this->success();
    }else{
      $this->error("密码错误");
    } 
  }
  /**
   * 显示权限管理
   * @access protected
   * @return object    $view        登录页面模板
   */
  public function auth(){
    return $this->fetch('auth', [
      'title' => '后台权限管理',
    ]);
  }
  /**
   * 获取角色列表
   * @access protected
   * @return object    $view        权限管理模板
   */
  public function get_station_list(){
    return Db::table('menu_station')->select();
  }
  /**
   * 删除岗位
   * @access protected
   * @param int     $id             岗位ID      
   * @return object                 执行结果
   */
  public function delete_station($id){
    Db::table('menu_station')->where('station_id', $id)->delete();
    $this->success("操作成功");
  }
  /**
   * 保存岗位信息
   * @access protected 
   * @return object                 执行结果
   */
  public function save_station(){
    $data = $_POST;
    // $station_id = $data['station_id'];
    // unset($data['station_id']);
    // if($station_id==0){
    //   $station = new MenuStationInfo();
    // }else{
    //   $station = MenuStationInfo::get($station_id);
    // }
    // if(empty($station)){
    //   $this->error("操作失败");
    // }else{
    //   $station->save($data);
    //   $this->success("操作成功");
    // }
    $is_conflict = Db::table('menu_station')->where('name', $data['name'])->find();
    if ($is_conflict) {
      $this->error('操作失败！已经有这个角色了。');
    } else {
      Db::table('menu_station')->insert($data);
      $this->success("操作成功");
    }
  }
  /**
   * 查询岗位已授权的菜单信息
   * @access protected 
   * @param int     $_GET           查询条件 
   * @return object                 获取的菜单列表
   */
  public function get_menu_list_by_station_id(){
    $station_id = $_GET['station_id'];
    // $station = MenuStationInfo::get($station_id);
    // if(empty($station)){
    //     $this->error("操作失败");
    // }
    $seek_list = Db::table('menu_seek')->where(['station_id'=>$station_id])->select();
    $recall = [];
    foreach ($seek_list as $key => $seek) {
      array_push($recall,[
                          'seek_id'=>$seek['seek_id'],
                          'name'=> Db::table('menu')->where('id', $seek['page_id'])->value('name'),
                          'page_id'=>$seek['page_id'],
                          ]
                  );
    }
    return $recall;
  }
  /**
   * 查询岗位可以添加的菜单信息
   * @access protected 
   * @param int     $_GET           查询条件 
   * @return object                 获取的菜单列表
   */
  public function get_able_select_by_station_id(){
    $station_id = $_GET['station_id'];

    // $station = MenuStationInfo::get($station_id);
    // if(empty($station)){
    //     $this->error("操作失败");
    // }
    $list = Db::table('menu_seek')->where(['station_id'=>$station_id])->select();
    $already_list = [];
    foreach ($list as $key => $menu) {
        array_push($already_list,$menu['page_id']);
    }
    $conditon['id'] =array('NOT IN',$already_list);
    $able_select_list=[];
    if(empty($already_list)){
        $able_select_list = Db::table('menu')->select();
    }else{
        $able_select_list = Db::table('menu')->where($conditon)->select();
    }
    return $able_select_list;
  }
  /**
   * 取消指定菜单的授权
   * @access protected 
   * @param int     $_POST['station_id']          岗位ID 
   * @param array     $_POST['id_list']           要取消授权的菜单ID数组 
   * @return object                 操作结果
   */
  public function station_remove_menu(){
      $station_id=$_POST['station_id'];
      // $station = MenuStationInfo::get($station_id);
      // if(empty($station)){
      //     $this->error("请选择角色");
      // }
      $iterm_id_list=$_POST['id_list'];
      foreach ($iterm_id_list as $key => $iterm_id){
          // $menu_seek = MenuSeekInfo::get(['station_id'=>$station_id,'seek_id'=>$iterm_id]);
          // if(!empty($menu_seek)){
          //     $menu_seek->delete();
          // }
          Db::table('menu_seek')->where('seek_id', $iterm_id)->delete();
      }		
      $this->success("操作成功");		
  }
  /**
   * 给岗位授权指定菜单
   * @access protected 
   * @param int     $_POST['station_id']          岗位ID 
   * @param array     $_POST['id_list']           要授权的菜单ID数组 
   * @return object                 操作结果
   */
  public function station_add_menu_iterm(){
      $station_id=$_POST['station_id'];
      // $station = MenuStationInfo::get($station_id);
      // if(empty($station)){
      //     $this->error("请选择角色");
      // }
      $iterm_id_list=$_POST['id_list'];
      foreach ($iterm_id_list as $key => $iterm_id){
          // $menu_seek = new MenuSeekInfo();
          // if(!empty($menu_seek)){
          //     $menu_seek->save(['station_id'=>$station_id,'page_id'=>$iterm_id]);
          // }
          Db::table('menu_seek')->insert([
            'station_id' => $station_id,
            'page_id' => $iterm_id
          ]);
      }		
      $this->success("操作成功");			
  }
  /**
   * 显示账号管理
   * @access protected
   * @return object    $view        登录页面模板
   */
  public function account(){
      $view=new View();
      $view->assign('title','账号管理');
      $class_list = Db::table('menu_station')
              ->select();
  $view->assign('class_list',$class_list);
      
      $station_list=Db::table('menu_station')->select();
      $view->assign('station_list',$station_list);
      return $view->fetch('account');
  }
  /**
   * 获取账号列表
   * @access protected
   * @return object    $list        账号列表
   */
  public function get_account_list($search_name,$user_station){
    $map =[];
    if(!empty($_GET['search_name'])){
        $map['name'] = ['like',"%$search_name%"];
    }
    if(!empty($_GET['user_station'])){
        $map['station_id'] = $user_station;
    }
    $list=Db::table('admin_user')->where($map)->select();
    foreach ($list as $key => $value) {
        $station = Db::table('menu_station')
                ->where('station_id',$value['station_id'])
                ->find();
        $list[$key]['station_name']=$station['name'];
    }
    return $list;
  }
  /**
     * 保存账号信息
     * @access protected
     * @return object    $code        操作结果
     */
    public function save_account(){
    $data =$_POST;
    unset($data['id']);
    $id = $_POST['id'];
    if($id==0){
            $data['password']=md5('111111');
      $id = Db::table('admin_user')->insertGetId($data);
    }else{
      Db::table('admin_user')->where('id',$id)->update($data);
    }
        $this->success('操作完成');
    }
    /**
     * 重置账户密码
     * @access protected
     * @return object 
     */
    public function psw_reset(){
        $id = $_POST['id'];
        $password = md5($_POST['new_password']);
    Db::table('admin_user')->where('id',$id)->update(['password'=>$password]);
        $this->success('操作完成');
    }
  /**
     * 删除账号信息
     * @access protected
     * @return object    $code        操作结果
     */
    public function del_user($id){
    $del_user = Db::table('admin_user')->delete(['id'=>$id]);
    $this->success("操作完成");
  }
  /**
   * 注销账户密码
   * @access protected
   * @return object 
   */
  public function log_out(){
      Session::set('qd_id',false);
      $this->redirect(QYBYB_WECHAT);
  }
}
