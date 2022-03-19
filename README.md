# LininSoftTech!

Hi! I'm your first Markdown file in **StackEdit**. If you want to learn about StackEdit, you can read me. If you want to play with Markdown, you can edit me. Once you have finished with me, you can create new files by opening the **file explorer** on the left corner of the navigation bar.


# **หน้าแรก**

1.  สินค้า
2.  ลูกค้า
3.  ปฏิบัติการ
	1. รับสินค้า
	2. ซัก
	3. อบ
	4. รีด
	5. พับแพค
	6. จัดเก็บ
4.  พลังงาน
5.  พนักงาน
6.  วิเคราะห์
7.  สต๊อก
8.  ตั้งค่า

## **ปฏิบัติการ**

 1. รับสินค้า
	 1. เลือกชื่อลูกค้า
	 2. ใส่จำนวนน้ำหนัก
 2. ซัก
	 2. เลือกพนักงาน
	 3. ผ้าทั่วไป ผ้าแก้ไข
	 4. เครื่องซักผ้า (เลือกตามน้ำหนักผ้า)
	 5. เลือกกองผ้าจากลูกค้าที่รับมาช่วงเช้า เช่น กองผ้า Customer A ขนาด 100กก.
	 6. ชนิดผ้า (ผ้าขน)
	 7. ผ้าเช็ดมือ
	 8. ใส่จำนวนกี่โลที่หยิบจากกองผ้าลูกค้า (ต้องไม่เกิน 100กก และไม่เกินขนาดตความจุ เครื่องซักผ้า)
	 9. เลือกสี 
	 10. ยืนยัน 


# Semantic Commit Messages

See how a minor change to your commit message style can make you a better programmer.

Format: `<type>(<scope>): <subject>`

`<scope>` is optional

## Example

```
feat: add hat wobble
^--^  ^------------^
|     |
|     +-> Summary in present tense.
|
+-------> Type: chore, docs, feat, fix, refactor, style, or test.
```

More Examples:

- `feat`: (new feature for the user, not a new feature for build script)
- `fix`: (bug fix for the user, not a fix to a build script)
- `docs`: (changes to the documentation)
- `style`: (formatting, missing semi colons, etc; no production code change)
- `refactor`: (refactoring production code, eg. renaming a variable)
- `test`: (adding missing tests, refactoring tests; no production code change)
- `chore`: (updating grunt tasks etc; no production code change)

References:

- https://www.conventionalcommits.org/
- https://seesparkbox.com/foundry/semantic_commit_messages
- http://karma-runner.github.io/1.0/dev/git-commit-msg.html