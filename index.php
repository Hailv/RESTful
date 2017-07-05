<?php
/**
 * Created by PhpStorm.
 * User: windd01
 * Date: 28/06/2017
 * Time: 13:25
 */
// Lớp dữ liệu.
class database
{
    private $conn;

// hàm kết nối
    function connect_db()
    {
        // thực hiện kết nối
        // nếu chưa kết nối thì thực hiện, nếu kết nối rồi thì bỏ qua
        if (!$this->conn) {
            $this->conn = new mysqli ('mysql', 'dev', 'dev', 'test') or die ('khong the ket noi');
            mysqli_set_charset($this->conn, 'utf8');
        }
    }

// hàm ngắt kết nối
    function disconnect_db()
    {
        mysqli_close($this->conn);
    }

// hàm hiển thị danh sách
    function select_staff($id)
    {
        if (!empty($id)) {
            // gán câu lệnh vào biến $sql
            // nếu có ID thì hiển thị nhân viên theo ID
            $sql = "select * from staff where id = $id";
            // thực thi câu lệnh $sql
            $result = $this->conn->query($sql);
            // Nếu ID >0 thì thực hiện, nếu không thì không thực hiện
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo Json_encode($row);
                }
            } else {
                echo "Khong co gia tri";
            }
        } else {
            // gán câu lệnh vào biến $sql
            // Nếu không có ID thì hiển thị tất cả
            $sql = "select * from staff";
            $data = array();
            // thực thi câu lệnh $sql
            $result = $this->conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                //echo Json_encode($row1);}
                $data[] = $row;
            }
            echo json_encode($data);
        }
    }

// hàm tạo mới
    function add_staff($name, $age, $phone)
    {
        // lấy giá trị trong body để thêm thông tin nhân viên
        $body = file_get_contents("php://input");
        parse_str($body);
        $sql = "insert into staff (name, age, phone)
          values ('$name','$age','$phone')";
        $result = $this->conn->query($sql);
        if ($result === TRUE) {
            // lấy giá trị ID cuối cùng để hiển thị
            $last_id = $this->conn->insert_id;
            $sql = "select * from staff where id = $last_id";
            $result = $this->conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo Json_encode($row);
                }
            } else {
                echo "Khong co gia tri";
            }

        }

        else {
            echo "tao moi khong thanh cong";
        }
    }

    // hàm xóa nhân viên
    function delete_staff($id)
    {
        // hiển thị nhân viên trước khi xóa
        $staff_before_delete = $this->select_staff($id);
        $sql = "delete from staff where id = $id";
        $result = $this->conn->query($sql);
        if ($result === TRUE) {
            echo $staff_before_delete;
        } else {
            echo "xoa khong thanh cong";
        }
    }

    //hàm sửa nhân viên
    function update_staff($id, $name, $age, $phone)
    {
        $body = file_get_contents("php://input");
        parse_str($body);
        $sql = "update staff set name ='$name', age ='$age', phone ='$phone' where id=$id";
        $result = $this->conn->query($sql);
        #echo $sql;
        if ($result === TRUE) {
            $this->select_staff($id);
        } else {
            echo " sua khong thanh cong";
        }

    }
}
//hàm khai báo biết, viết lại url và thực thi
class content
{  private $method;
    public $url;
    public $parameters;
    function get_method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    function get_url()
    {
        $this->url = explode('/', $_GET['id']);
        return $_GET['id'];
    }
}
$content = new content();
#var_dump($content->get_method());
if ($content->get_method() == 'GET')
{
    $get = new database();
    $get->connect_db();
    $get->select_staff(''.$_GET['id']);
    $get->disconnect_db();
}
else if ($content->get_method() == 'POST')
{
        $get = new database();
        $get->connect_db();
        $get->add_staff($name,$age,$phone);
        $get->disconnect_db();
}
else if ($content->get_method() == 'PUT')
{
        $get = new database();
        $get->connect_db();
        $get->update_staff('' . $_GET['id'],$name,$age,$phone);
        $get->disconnect_db();
}
else if ($content->get_method() == 'DELETE')
{
        $get = new database();
        $get->connect_db();
        $get->delete_staff('' . $_GET['id']);
        $get->disconnect_db();
}
