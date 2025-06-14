# NPTU Class 更新紀錄

## 📅 目錄（點擊跳至對應日期）

- [20250424](#20250424)
- [20250425](#20250425)
- [20250427](#20250427)
- [20250503](#20250503)
- [20250504](#20250504)
- [20250507](#20250507)
- [20250508](#20250508)
- [20250510](#20250510)
- [20250511](#20250511)
- [20250512](#20250512)
- [20250513](#20250513)
- [20250514](#20250514)
- [20250515](#20250515)
- [20250516](#20250516)
- [20250517](#20250517)
- [20250518](#20250518)
- [20250525](#by-ziz-20250525-1600)


---

## 20250424
---

### 新增 login、register 頁面  
### 新增 components 資料夾（把共同的 code 寫在裡面，範例：nav、footer）  
### `database.php` 修改為本地端，尚未測試是否正常  
### 下次完成 login 跟 register 頁面或許會完善？  

#### 負責人: ZJ 2025/04/24 23:09:38

---

## 20250425
---

### 新增 `register.php` 後端，`login` 後端微調  
- `login.php` 增加權限判斷（admin or user）  
- 可能要新增 admin 後臺管理頁面？因為有分權限  
- `register` 的 username 還沒加  

#### by ziz 2025/04/26 00:47

---

## 20250427
---

### register 跟 login 大致完成，錯誤提示用 alert  
### 後台 dashboard 新增 sidebar，可顯示不同 admin 名字  
### `action/common.php` 建立放常用 function  
### 輸入框新增必填限制防止出錯  

![dashboard](readmeimg/dashboard.png)  
![dashboard2](readmeimg/dashboard2.png)

#### 負責人: ZJ 2025/04/27 09:45

---

### 新增後臺 管理員可調整權限（僅有畫面，功能未實裝）  
### 新增後台 product 相關畫面（未完成，詢問 ziz03）  
### 後台新增麵包屑功能

#### 負責人: ZJ 2025/04/27 22:28

---

### 新增 user center（尚未完成）

#### 負責人: ZJ 2025/04/27 22:28

---

## 20250503
---

### 修改 logo（image/blackLOGO.png）  
### 修正一些 bug  
### 新增 forgotPassword  

![forgot](readmeimg/1280.jpg)

#### 負責人: ZJ 2025/05/03 00:00

---

### 修正 logo 問題，新增購物車前後端（尚未完善）與首頁按鈕  
### icon 靈異事件  
#### by ziz 2025/05/03 15:00

---

### 已修正 logo 問題（nav include 的位置調整）  

![logo](readmeimg/images.jpg)

#### by ZJ 2025/05/03 16:00

---

### admin 後台：商品列表和新增商品完成（可再優化）  
- `product.php` 可查看商品詳情  
- 首頁「查看詳情」功能完成，但排版尚需改善  
- 接下來要做購物車與結帳流程  
#### by ziz 2025/05/03 23:04

---

### 修改 `product.php` 排版  
#### by ZJ 2025/05/03 23:59

---

## 20250504
---

### `product.php` 排版優化、加入刪除功能（編輯未完成）  
### `index.php` 顯示 product 資料表內容  
### `product_add.php` 支援圖片上傳至 image 資料夾並儲存資料庫路徑  

#### by ZJ 2025/05/04 23:16

---

## 20250507
---

### 修改 `product.php` 查看詳情路徑錯誤問題  

#### by ZJ 2025/05/07 23:53

---

## 20250508
---

### 修改 `cart.php` 可更新商品數量  
### 新增 `action/cart.php` update 方法  
### 新增 `admin/products.php` 更改庫存功能  

#### by 77 2025/05/08 00:54

---

### `admin/products.php` 支援修改商品名稱與金額  
### 整合按鈕：名稱、金額、庫存一鍵修改  

![商品管理](readmeimg/ProductManagement.png)

#### by 77 2025/05/08 01:13

---

### 新增 `admin/changestatus.php` 修改使用者權限功能  

![使用者狀態](readmeimg/UseDate.png)

#### by 77 2025/05/08 02:13

---

### 調整資料表關聯限制，可刪除  
- 新增結帳前後端，但送出未完成  
- 搜尋與分頁功能待開發（課堂要求）  
- 預計加入 30 筆資料（呈現方式待討論）  
- 首頁新增「關於我們」nav（已建 table）  
- nav icon 尚未處理  

#### by ziz 2025/05/08 14:00

---

### 修改 `cart.php` 數量更新樣式，移除按鈕改為 icon  

![購物車](readmeimg/cart.png)

#### by 77 2025/05/08 16:11

---

### `index.php` 新增搜尋框功能，邏輯在 `action/common.php`  

![搜尋](readmeimg/Search.png)

#### by ZJ 2025/05/08 22:29

---

### `changestatus.php` 新增使用者編輯與刪除功能  

![功能](readmeimg/userupdate.png)

#### by 77 2025/05/08 22:58

---

### 修改 `products.php` 商品依 ID 小到大排序  

#### by 77 2025/05/08 23:10

---

## 20250510
---

### 新增 nav icon 與結帳送出功能  
- admin 後台新增訂單明細查看功能  
- 每頁顯示 5 筆並支援分頁與搜尋  
- 預計首頁加入所有商品，支援搜尋與分頁  
- 使用者可在購物車中直接輸入數量  
- admin 後台首頁考慮美化  

#### by ziz 2025/05/10 01:12

---

### 修改 `admin/compoents/sidebar.php` 收縮問題  
### 新增 `Allproduct.php` 顯示所有產品（尚未新增資料）  
### 修改 `action/common.php` 的 `displayProductsList` 方法支援 `limit` 傳入  

#### by ZJ 2025/05/10 12:27
---
### `allproduct`裡面分頁功能改好了(一頁limit=3)
- `forgotpwd`的bug修好了
### by ziz 2025/05/10  23:22
---
## 20250511
---
### 新增 cart.php 購物車庫存顯示功能 
![功能](readmeimg/cart1.png)
### 修改 action/cart.php "update" function
### 新增 action/checkout_process.php 庫存檢查 function
### 新增 checkout.php 庫存不足無法下單及警告提示
![功能](readmeimg/checkout.png)

### by 77 2025/05/11 00:02
### admin後臺管理查看訂單增加可以看到商品名稱並修正ID一律從1開始increment
- admin/products.php也是ID從1開始，並增加分頁功能，還有搜尋功能。另外搜尋以後還有跳轉回一開始的按鈕
- 所有商品裡面的分頁功能也弄好了，還有+可以輸入頁碼跳轉跟回到首頁
- 所有產品的搜尋功能也有增加回到所有產品首頁的按鈕
- 美觀性可以再討論
![功能](readmeimg/products.png)

![功能](readmeimg/orders.png)

- Allproduct.php原本要Login現在不用了
by ziz 2025/05/11 19:10
---
###  dashboard.php 新增關於我們(尚未完善)
### by ZJ 2025/05/11 23:32
---
## 20250512
---
### 美化商品查看詳情裡面的排版  
### 首頁排版美觀
### index的搜尋 fixed
### by ziz 2025/05/12 21:15

---
### 新增關於我可以修改頁面  `admin/changeaboutme.php` (尚未完善)

### by ZJ 2025/05/12 23:29
### 登入、註冊、忘記密碼、會員中心美化排版完成
- 首頁有稍微再變動
### by ziz 2025/05/12 23:20
---
## 20250513
---
### index推薦好書和上面標語整體重新排版美觀
### 登入夜面等右邊的中文字弄成跟英文一樣
### 後臺管理頁面的商品管理列表美化設計
### by ziz 2025/05/13 13:36
---
## 20250514
---
### 後台的多數都美觀了，新增商品還沒因為GPT低能兒
### 訂單列表也有稍微的美化，但也許可以更好
### by ziz 2025/05/14 22:58
---
## 20250515
---
### 新增修改關於我功能 `admin/changeaboutme.php` && `action/update_aboutme.php` 
### 美觀功能需由老頂了 ZIZ來改
### by ZJ 2025/05/15 00:20
---
### 首頁新增下方的今日金句，隨刷新隨機出現，並加入淡淡動畫。
- 可以再提出有甚麼地方可以改進，美觀?動畫?排版? and so on.
### 首頁有加一點淡動畫，顯示商品的卡片也有修改字體顏色跟排版，也有一點小動畫。
- 但這邊顏色感覺可以再想，GPT給的我都不滿意，快沒法子了。
### by ziz 2025/05/15 12:55
---
### 新增書籍isbn and classification 目前看來應該沒太大問題
- show在產品那邊也改好了
- 資料庫也弄好重新匯出
### by ziz 2025/05/15 23:10
---
### 修改sidebar 顯示  
### by ZJ 2025/05/15 23:34
---
### 20250516
---
### 修改sidebar 顯示  
### by ZJ 2025/05/16 22:40
---
### 購物車調整美觀，後台查看訂單美化一些
- 新增商品也美觀了，新增分類和isbn昨天忘記+
### by ziz 2025/05/16 23:35
---
### 20250517
---
### sidebar 再次調整
### 修改`admin/products.php`跟`admin/view_order.php`時間變成 'YYYY-MM-DD -HH-mm'
### by ZJ 2025/05/17 00:05
### 修改products.php更新資料按鈕(直接暴力加br)
- 時間那邊的-hh-mm我改成-hh:mm不然看了好亂稍微區隔
### by ziz 2025/05/17 13:00
---
### userCenter新增查看訂單的tabs
- admin/view_order.php新增可以改訂單狀態，同步顯示到userCenter.php
- action/update_vieworders.php是改vieworder的狀態下拉選單的後端
- userCenter應該可以在訂單狀態那邊案取消訂單，但還沒想好怎麼搞，像是他按下去發送這個request，admin可以再查看訂單那邊view，然後confirm或reject之類的
### by ziz 2025/05/17 16:24
---
### 20250518
---
### 美化 `userCenter.php` 
![userCenter](readmeimg/userCenter1.png)

![userCenter](readmeimg/userCenter2.png)
### by ZJ 2025/05/18 00:38
---
### 修改 `userCenter.php`  沒有帳號的問題

### by ZJ 2025/05/18 11:23
---
### 所有產品美化再改
- 首頁美化目前先這樣?顏色也許可以再改，然後所有產品下面那個分業按鈕那邊有不一致但claude用完了沒辦法改
### by ziz 2025/05/23 22:05
---
### 美化購物車
- 改一些中文字，index.php下面多加一個點進所有商品
- 後臺管理的首頁改成show資訊的面板，抓db，更改權限改成帳號資訊
### by ziz 2025/05/25 1600