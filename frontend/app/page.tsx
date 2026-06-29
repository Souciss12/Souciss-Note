'use client'

import { useRef } from 'react'
import { ForwardRefEditor } from './components/ForwardRefEditor'
import type { MDXEditorMethods } from '@mdxeditor/editor'

export default function Home() {
  const editorRef = useRef<MDXEditorMethods>(null)

  return (
    <main style={{ maxWidth: 800, margin: '40px auto', padding: 16 }}>
      <h1>MDX Editor</h1>

      <ForwardRefEditor
        ref={editorRef}
        markdown="# Hi"
        onChange={(markdown) => console.log(markdown)}
      />
    </main>
  )
}