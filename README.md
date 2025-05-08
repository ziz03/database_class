NPTU class 需要 
===
----
2025/04/24
--- 
### 新增login、register 頁面 
### 新增compoents 資料夾(把共同的code 寫在裡面範例:nav 、footer)
### database.php 修改為本地端 還沒測試是否正常 
### 下次完成 login 跟register 頁面或許會完善?

#### 負責人: ZJ  2025/04/24 23:09:38
---
2025/04/25
### 新增register.php後端，login後端有微調
-  login.php增加權限的條件判斷(admin or user?)
-  可能要新增admin的後臺管理頁面?因為有分權限;register的user name 還沒加
#### by ziz 4/26 00:47
---
2025/04/27
###  register跟login 大蓋好了也把錯誤那邊的修正用alert去提醒user
###  後台dashboard 以新增sidebar 後台也會隨著不同的admin 人員去顯示他的名字
###  action/common.php 用來放常用的function
###  輸入框都有新增強制要求user 輸入才不會出錯
![ 图标](readmeimg/dashboard.png)

![ 图标](readmeimg/dashboard2.png)

#### 負責人: ZJ  2025/04/27 09:45

### 新增後台 管理員 可以調整權限 (目前只有畫面尚未實裝function )
### 新增後台product 不知道該如何呈現 請詳問 ziz03
### 後台新增麵包屑可以讓管理員知道他在哪裡

#### 負責人: ZJ  2025/04/27 22:28

### 新增user center 還沒完全弄好
#### 負責人: ZJ  2025/04/27 22:28
---
2025/05/03
### 修改logo (image/blackLOGO.png)
### 修點bug
### 新增forgotPassword
![ 图标](readmeimg/1280.jpg)

#### 負責人: ZJ  2025/05/03 00:
---
### 修正logo，增加購物車前後端(尚未完善)和首頁加入購物車按鈕
### icon靈異事件
#### by ziz 2025/05/03 15

### 已修正logo可能是include 'compoents/nav.php'; 放在最上面所導致的 應該要放在 body 標籤內的最上面
![ 图标](readmeimg/images.jpg)

### by ZJ 2025/05/03 16:00
----
### admin後臺管理的商品列表和新增商品已經搞定(也許可以再調整)
- 有加一個product.php是放查看詳請的內容
- 首頁點選查看詳情的時候已經能跳出東西，但是還要思考怎麼排版美化。
- 上面弄好在弄購物車和結帳。
#### by ziz 2025/05/03 23:04
---
### 修改product.php 排版

#### by ZJ 2025/05/03 23:59
---
### 修改product.php 排版 及新增刪除功能(編輯功能還沒做)
### 修改index.php 可以去抓product的資料表顯示出來
### 修改product_add.php 可以上傳圖片到指定資料夾(image)資料庫也抓的到位置

#### by ZJ 2025/05/04 23:16
---
### 修改查看詳情product.php 感覺是路徑問題所以修改再傳入到img標籤之前修改一下路徑


#### by ZJ 2025/05/07 23:53
---
### 修改cart.php 更新數量 
### 新增action/cart.php update 方法
### 新增admin/products.php 更改庫存數量功能

### by 77 2025/05/08 00:54
---
### 新增admin/products.php 商品名稱&&商品金額更新功能
### 修改admin/products.php 將商品名稱&&商品金額&&庫存數量 按鈕整合成一個
![ 图标](readmeimg/ProductManagement.png)

### by 77 2025/05/08 01:13
---
### 新增admin/changestatus.php 修改使用者權限功能
![ 图标](readmeimg/UseDate.png)

### by 77 2025/05/08 02:13
---
### table已修改關聯限制，可以刪除了有問題再提出
- 新增結帳前後端，但是結帳的送出還沒有搞
- 可能要新增搜尋和分頁功能(課堂要求)
- 然後資料最少要30筆這應該好解決(怎麼呈現出來比較美觀?)
- 考慮首頁新增關於我們的nav(table zj 已經建)
- nav 加icon還沒弄
### by ziz 2025/5/8 1400
---
### 修改 cart.php 的數量更新樣式 及 移除按鈕改為icon
![圖標](readmeimg/cart.png)

### by 77 2025/05/08 16:11
---
### 修改 index.php 會有搜尋框可以搜尋書籍 function 在 action/common.php 裡面 
![搜尋](readmeimg/Search.png) 
### by ZJ 2025/05/08 22:29

---