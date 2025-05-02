import shutil
import os

# Plugin 資料夾名稱
PLUGIN_FOLDER = "."

# 輸出檔案名稱（不含副檔名）
OUTPUT_ZIP_NAME = "banner-slider"

# 當前路徑下確認 Plugin 資料夾存在
if not os.path.isdir(PLUGIN_FOLDER):
    print(f"❌ 找不到資料夾：{PLUGIN_FOLDER}")
    exit(1)

# 建立 ZIP
shutil.make_archive(OUTPUT_ZIP_NAME, 'zip', PLUGIN_FOLDER)
print(f"✅ 成功封裝：{OUTPUT_ZIP_NAME}.zip")
