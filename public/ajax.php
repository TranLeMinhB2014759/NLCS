<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
    if (isset($_GET['value'])) {
        $value = $_GET['value'];
        $htmlResult = '';
        $query_pm_list = $db->prepare('SELECT * FROM phieumuon WHERE pm_stt = :pm_stt');
        $query_pm_list->bindValue(':pm_stt', $value);
        $query_pm_list->execute();
        $results_pm_list = $query_pm_list->fetchAll();
        echo '<div class="modal_ajax">
                <table>
                    <tr>
                        <th>Đầu sách</th>
                        <th>Mã sách</th>
                    </tr>';
                        foreach ($results_pm_list as $list) {
                            $book_stt_list = explode(", ", $list['book_stt']);
                            $title_id_list = explode(", ", $list['title_id']);
                            foreach ($book_stt_list as $key => $value_book_stt) {
                                $value_title_id = $title_id_list[$key];
                                echo "<tr><td>" . $value_title_id . " </td> " . "<td> CNTT." . $value_title_id . str_pad($value_book_stt, 3, '0', STR_PAD_LEFT) . "</td></tr>";
                            }
                        }
        echo '  </table>
            </div>';
    }
    if (isset($_POST['student'])) {

    }
?>